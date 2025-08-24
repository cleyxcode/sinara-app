<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IvaTestResult;
use App\Models\UserApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class IvaTestApiController extends Controller
{
    /**
     * Submit IVA test result
     */
    public function submitResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'examination_date' => 'required|date|before_or_equal:today',
            'examination_type' => 'required|string|max:255',
            'result' => 'required|in:positif,negatif',
            'notes' => 'nullable|string|max:1000',
            'examined_by' => 'nullable|string|max:255',
        ], [
            'examination_date.required' => 'Tanggal pemeriksaan wajib diisi',
            'examination_date.date' => 'Format tanggal pemeriksaan tidak valid',
            'examination_date.before_or_equal' => 'Tanggal pemeriksaan tidak boleh lebih dari hari ini',
            'examination_type.required' => 'Jenis pemeriksaan wajib diisi',
            'examination_type.max' => 'Jenis pemeriksaan maksimal 255 karakter',
            'result.required' => 'Hasil pemeriksaan wajib diisi',
            'result.in' => 'Hasil pemeriksaan harus positif atau negatif',
            'notes.max' => 'Catatan maksimal 1000 karakter',
            'examined_by.max' => 'Nama pemeriksa maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Check if user already has a test result for the same date and type
            $existingResult = IvaTestResult::where('user_id', $user->id)
                ->where('examination_date', $request->examination_date)
                ->where('examination_type', $request->examination_type)
                ->first();

            if ($existingResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki hasil pemeriksaan untuk tanggal dan jenis pemeriksaan yang sama'
                ], 409);
            }

            $testResult = IvaTestResult::create([
                'user_id' => $user->id,
                'examination_date' => $request->examination_date,
                'examination_type' => $request->examination_type,
                'result' => $request->result,
                'notes' => $request->notes,
                'examined_by' => $request->examined_by,
            ]);

            // Load user relationship for response
            $testResult->load('user:id,name,email,fasilitas_kesehatan');

            return response()->json([
                'success' => true,
                'message' => 'Hasil pemeriksaan IVA berhasil disimpan',
                'data' => [
                    'test_result' => [
                        'id' => $testResult->id,
                        'examination_date' => $testResult->formatted_examination_date,
                        'examination_type' => $testResult->examination_type,
                        'result' => $testResult->result_in_indonesian,
                        'notes' => $testResult->notes,
                        'examined_by' => $testResult->examined_by,
                        'submitted_at' => $testResult->created_at->format('d/m/Y H:i:s'),
                        'user' => [
                            'name' => $testResult->user->name,
                            'email' => $testResult->user->email,
                            'health_facility' => $testResult->user->fasilitas_kesehatan,
                        ]
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hasil pemeriksaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's test history
     */
    public function getUserHistory(Request $request)
    {
        try {
            $user = $request->user();
            
            $results = IvaTestResult::where('user_id', $user->id)
                ->with('user:id,name,email,fasilitas_kesehatan')
                ->orderBy('examination_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedResults = $results->map(function ($result) {
                return [
                    'id' => $result->id,
                    'examination_date' => $result->formatted_examination_date,
                    'examination_type' => $result->examination_type,
                    'result' => $result->result_in_indonesian,
                    'result_status' => $result->result,
                    'notes' => $result->notes,
                    'examined_by' => $result->examined_by,
                    'submitted_at' => $result->created_at->format('d/m/Y H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Riwayat pemeriksaan berhasil diambil',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'health_facility' => $user->fasilitas_kesehatan,
                    ],
                    'test_results' => $formattedResults,
                    'total_results' => $results->count(),
                    'positive_count' => $results->where('result', 'positif')->count(),
                    'negative_count' => $results->where('result', 'negatif')->count(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat pemeriksaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific test result detail
     */
    public function getResultDetail(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $result = IvaTestResult::where('id', $id)
                ->where('user_id', $user->id)
                ->with('user:id,name,email,fasilitas_kesehatan')
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail hasil pemeriksaan berhasil diambil',
                'data' => [
                    'test_result' => [
                        'id' => $result->id,
                        'examination_date' => $result->formatted_examination_date,
                        'examination_type' => $result->examination_type,
                        'result' => $result->result_in_indonesian,
                        'result_status' => $result->result,
                        'notes' => $result->notes,
                        'examined_by' => $result->examined_by,
                        'submitted_at' => $result->created_at->format('d/m/Y H:i:s'),
                        'updated_at' => $result->updated_at->format('d/m/Y H:i:s'),
                    ],
                    'user' => [
                        'name' => $result->user->name,
                        'email' => $result->user->email,
                        'health_facility' => $result->user->fasilitas_kesehatan,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail hasil pemeriksaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update test result
     */
    public function updateResult(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'examination_date' => 'sometimes|date|before_or_equal:today',
            'examination_type' => 'sometimes|string|max:255',
            'result' => 'sometimes|in:positif,negatif',
            'notes' => 'nullable|string|max:1000',
            'examined_by' => 'nullable|string|max:255',
        ], [
            'examination_date.date' => 'Format tanggal pemeriksaan tidak valid',
            'examination_date.before_or_equal' => 'Tanggal pemeriksaan tidak boleh lebih dari hari ini',
            'examination_type.max' => 'Jenis pemeriksaan maksimal 255 karakter',
            'result.in' => 'Hasil pemeriksaan harus positif atau negatif',
            'notes.max' => 'Catatan maksimal 1000 karakter',
            'examined_by.max' => 'Nama pemeriksa maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            
            $result = IvaTestResult::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan tidak ditemukan'
                ], 404);
            }

            // Check if result was submitted more than 24 hours ago
            if ($result->created_at->diffInHours(now()) > 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan hanya dapat diubah dalam 24 jam setelah pengiriman'
                ], 403);
            }

            $updateData = array_filter($request->only([
                'examination_date', 
                'examination_type', 
                'result', 
                'notes', 
                'examined_by'
            ]));

            $result->update($updateData);
            $result->load('user:id,name,email,fasilitas_kesehatan');

            return response()->json([
                'success' => true,
                'message' => 'Hasil pemeriksaan berhasil diperbarui',
                'data' => [
                    'test_result' => [
                        'id' => $result->id,
                        'examination_date' => $result->formatted_examination_date,
                        'examination_type' => $result->examination_type,
                        'result' => $result->result_in_indonesian,
                        'result_status' => $result->result,
                        'notes' => $result->notes,
                        'examined_by' => $result->examined_by,
                        'submitted_at' => $result->created_at->format('d/m/Y H:i:s'),
                        'updated_at' => $result->updated_at->format('d/m/Y H:i:s'),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui hasil pemeriksaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete test result
     */
    public function deleteResult(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $result = IvaTestResult::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan tidak ditemukan'
                ], 404);
            }

            // Check if result was submitted more than 1 hour ago
            if ($result->created_at->diffInHours(now()) > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil pemeriksaan hanya dapat dihapus dalam 1 jam setelah pengiriman'
                ], 403);
            }

            $result->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hasil pemeriksaan berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus hasil pemeriksaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get examination types for dropdown
     */
    public function getExaminationTypes()
    {
        $types = [
            'IVA Test',
            'Pap Smear',
            'HPV Test',
            'Kolposkopi',
            'Biopsi',
            'Pemeriksaan Lanjutan'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Jenis pemeriksaan berhasil diambil',
            'data' => [
                'examination_types' => $types
            ]
        ], 200);
    }
}