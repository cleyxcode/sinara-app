<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserResponse;
use App\Models\Question;
use App\Models\UserApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\FacilityIva;

class ResponseApiController extends Controller
{
    public function submitResponses(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'responses' => 'required|array|min:1',
                'responses.*.question_id' => 'required|integer|exists:questions,id',
                'responses.*.selected_option' => 'required|integer|min:0',
                'session_id' => 'nullable|string',
                'user_latitude' => 'nullable|numeric|between:-90,90',
                'user_longitude' => 'nullable|numeric|between:-180,180'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $existingResponse = UserResponse::where('user_id', $user->id)
                ->whereDate('completed_at', Carbon::today())
                ->first();

            if ($existingResponse) {
                // Jika sudah ada response hari ini, tetap berikan saran fasilitas jika risk level tinggi
                $facilitySuggestions = [];
                if ($existingResponse->risk_level === 'Sedang-Tinggi') {
                    $facilitySuggestions = $this->getFacilitySuggestions($user, $request);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah mengisi tes hari ini. Silakan coba lagi besok.',
                    'data' => [
                        'existing_response' => [
                            'id' => $existingResponse->id,
                            'total_score' => $existingResponse->total_score,
                            'risk_level' => $existingResponse->risk_level,
                            'completed_at' => $existingResponse->completed_at
                        ],
                        'facility_suggestions' => $facilitySuggestions
                    ]
                ], 409);
            }

            DB::beginTransaction();

            $questionIds = collect($request->responses)->pluck('question_id');
            $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

            $totalScore = 0;
            $validatedResponses = [];

            foreach ($request->responses as $response) {
                $question = $questions->get($response['question_id']);
                
                if (!$question) {
                    throw new \Exception("Pertanyaan dengan ID {$response['question_id']} tidak ditemukan");
                }

                $options = $question->options;
                if (!isset($options[$response['selected_option']])) {
                    throw new \Exception("Opsi jawaban tidak valid untuk pertanyaan ID {$response['question_id']}");
                }

                $selectedOption = $options[$response['selected_option']];
                $score = $selectedOption['score'] ?? 0;
                $totalScore += $score;

                $validatedResponses[] = [
                    'question_id' => $response['question_id'],
                    'question_text' => $question->question_text,
                    'category' => $question->category,
                    'selected_option_index' => $response['selected_option'],
                    'selected_option_text' => $selectedOption['text'],
                    'score' => $score
                ];
            }

            $riskLevel = UserResponse::determineRiskLevel($validatedResponses);
            $recommendations = UserResponse::generateRecommendations($riskLevel, $validatedResponses);

            $userResponse = UserResponse::create([
                'user_id' => $user->id,
                'session_id' => $request->session_id,
                'responses' => $validatedResponses,
                'total_score' => $totalScore,
                'risk_level' => $riskLevel,
                'recommendations' => $recommendations,
                'completed_at' => Carbon::now()
            ]);

            // Ambil saran fasilitas jika risk level Sedang-Tinggi
            $facilitySuggestions = [];
            if ($riskLevel === 'Sedang-Tinggi') {
                $facilitySuggestions = $this->getFacilitySuggestions($user, $request);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan dan hasil telah dihitung',
                'data' => [
                    'response_id' => $userResponse->id,
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'alamat' => $user->alamat,
                        'umur' => $user->umur,
                        'fasilitas_kesehatan' => $user->fasilitas_kesehatan
                    ],
                    'test_result' => [
                        'total_questions' => count($validatedResponses),
                        'total_score' => $totalScore,
                        'risk_level' => $riskLevel,
                        'recommendations' => $recommendations,
                        'completed_at' => $userResponse->completed_at
                    ],
                    'responses' => $validatedResponses,
                    'facility_suggestions' => $facilitySuggestions
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses jawaban',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserHistory(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $responses = UserResponse::where('user_id', $user->id)
                ->orderBy('completed_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat tes berhasil diambil',
                'data' => [
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'alamat' => $user->alamat,
                        'umur' => $user->umur,
                        'fasilitas_kesehatan' => $user->fasilitas_kesehatan
                    ],
                    'history' => $responses->items(),
                    'pagination' => [
                        'current_page' => $responses->currentPage(),
                        'last_page' => $responses->lastPage(),
                        'per_page' => $responses->perPage(),
                        'total' => $responses->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil riwayat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getFacilitySuggestions($user, $request): array
    {
        $suggestions = [];
        $maxSuggestions = 3;

        try {
            // Prioritas 1: Berdasarkan koordinat user (jika ada)
            if ($request->has('user_latitude') && $request->has('user_longitude')) {
                $nearbyFacilities = FacilityIva::active()
                    ->nearBy($request->user_latitude, $request->user_longitude, 25)
                    ->limit($maxSuggestions)
                    ->get();
                
                if ($nearbyFacilities->isNotEmpty()) {
                    return [
                        'message' => 'Fasilitas IVA terdekat dari lokasi Anda:',
                        'facilities' => $nearbyFacilities->toArray(),
                        'total_suggestions' => $nearbyFacilities->count(),
                        'note' => 'Silakan hubungi fasilitas untuk membuat janji temu.'
                    ];
                }
            }

            // Prioritas 2: Berdasarkan alamat user
            if ($user->alamat) {
                $facilitiesByUserLocation = FacilityIva::active()
                    ->where(function($query) use ($user) {
                        // Cari berdasarkan kata kunci dalam alamat
                        $alamatWords = explode(' ', $user->alamat);
                        foreach ($alamatWords as $word) {
                            if (strlen($word) > 3) { // hanya kata yang lebih dari 3 huruf
                                $query->orWhere('location', 'LIKE', '%' . $word . '%');
                                $query->orWhere('address', 'LIKE', '%' . $word . '%');
                            }
                        }
                    })
                    ->limit($maxSuggestions)
                    ->get();
                
                if ($facilitiesByUserLocation->isNotEmpty()) {
                    return [
                        'message' => 'Fasilitas IVA di sekitar alamat Anda:',
                        'facilities' => $facilitiesByUserLocation->toArray(),
                        'total_suggestions' => $facilitiesByUserLocation->count(),
                        'note' => 'Silakan hubungi fasilitas untuk membuat janji temu.'
                    ];
                }
            }

            // Prioritas 3: Fasilitas dengan pelatihan terbaru (fallback)
            $facilitiesWithTraining = FacilityIva::active()
                ->whereNotNull('iva_training_years')
                ->orderBy('location')
                ->limit($maxSuggestions)
                ->get();
            
            return [
                'message' => 'Fasilitas IVA yang tersedia:',
                'facilities' => $facilitiesWithTraining->toArray(),
                'total_suggestions' => $facilitiesWithTraining->count(),
                'note' => 'Silakan hubungi fasilitas untuk membuat janji temu.'
            ];

        } catch (\Exception $e) {
            return [
                'message' => 'Silakan konsultasi ke fasilitas kesehatan terdekat.',
                'facilities' => [],
                'total_suggestions' => 0,
                'note' => 'Gagal mengambil daftar fasilitas kesehatan.',
                'error' => $e->getMessage()
            ];
        }
    }
}