<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacilityIva;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class FacilityIvaApiController extends Controller
{
    /**
     * Get all active facilities
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = FacilityIva::active();

            // Filter by location if provided
            if ($request->has('location') && !empty($request->location)) {
                $query->byLocation($request->location);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $facilities = $query->orderBy('location')
                               ->orderBy('name')
                               ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Fasilitas IVA berhasil diambil',
                'data' => $facilities->items(),
                'pagination' => [
                    'current_page' => $facilities->currentPage(),
                    'total' => $facilities->total(),
                    'per_page' => $facilities->perPage(),
                    'last_page' => $facilities->lastPage(),
                    'has_more' => $facilities->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data fasilitas IVA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nearby facilities based on coordinates
     */
    public function getNearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:200'
        ]);

        try {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->get('radius', 50); // default 50 km
            $limit = $request->get('limit', 10);

            $nearbyFacilities = FacilityIva::active()
                ->nearBy($latitude, $longitude, $radius)
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Fasilitas terdekat berhasil ditemukan',
                'data' => [
                    'user_location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ],
                    'search_radius_km' => $radius,
                    'facilities' => $nearbyFacilities,
                    'total_found' => $nearbyFacilities->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari fasilitas terdekat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get facilities by location
     */
    public function getByLocation(Request $request, $location): JsonResponse
    {
        try {
            $facilities = FacilityIva::active()
                ->byLocation($location)
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => "Fasilitas IVA di {$location} berhasil diambil",
                'data' => [
                    'location' => $location,
                    'facilities' => $facilities,
                    'total' => $facilities->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil fasilitas berdasarkan lokasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available locations
     */
    public function getLocations(): JsonResponse
    {
        try {
            $locations = FacilityIva::active()
                ->select('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location');

            return response()->json([
                'status' => 'success',
                'message' => 'Lokasi tersedia berhasil diambil',
                'data' => [
                    'locations' => $locations,
                    'total' => $locations->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil daftar lokasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get facility detail
     */
    public function show($id): JsonResponse
    {
        try {
            $facility = FacilityIva::active()->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail fasilitas berhasil diambil',
                'data' => $facility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fasilitas tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Search facilities
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100'
        ]);

        try {
            $query = $request->get('query');
            $facilities = FacilityIva::active()
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('location', 'LIKE', "%{$query}%")
                      ->orWhere('address', 'LIKE', "%{$query}%");
                })
                ->orderBy('location')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Pencarian berhasil',
                'data' => [
                    'query' => $query,
                    'facilities' => $facilities,
                    'total_found' => $facilities->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan pencarian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get facilities statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_facilities' => FacilityIva::active()->count(),
                'total_locations' => FacilityIva::active()->distinct('location')->count(),
                'facilities_with_training' => FacilityIva::active()
                    ->whereNotNull('iva_training_years')
                    ->count(),
                'facilities_with_coordinates' => FacilityIva::active()
                    ->withCoordinates()
                    ->count(),
                'by_location' => FacilityIva::active()
                    ->selectRaw('location, COUNT(*) as count')
                    ->groupBy('location')
                    ->orderBy('count', 'desc')
                    ->get()
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Statistik fasilitas IVA berhasil diambil',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suggest nearby facilities for user after screening
     */
    public function getSuggestionAfterScreening(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $suggestions = [];

            // Cari berdasarkan fasilitas kesehatan user jika ada
            if ($user && !empty($user->fasilitas_kesehatan)) {
                $facilitiesByUserLocation = FacilityIva::active()
                    ->byLocation($user->fasilitas_kesehatan)
                    ->orderBy('name')
                    ->limit(3)
                    ->get();
                
                if ($facilitiesByUserLocation->isNotEmpty()) {
                    $suggestions = $facilitiesByUserLocation;
                }
            }

            // Jika ada koordinat, cari yang terdekat
            if ($request->has('latitude') && $request->has('longitude')) {
                $nearbyFacilities = FacilityIva::active()
                    ->nearBy($request->latitude, $request->longitude, 25)
                    ->limit(5)
                    ->get();
                
                if ($nearbyFacilities->isNotEmpty()) {
                    $suggestions = $nearbyFacilities;
                }
            }

            // Fallback: ambil fasilitas populer dengan pelatihan
            if (empty($suggestions->toArray())) {
                $suggestions = FacilityIva::active()
                    ->whereNotNull('iva_training_years')
                    ->orderBy('location')
                    ->orderBy('name')
                    ->limit(5)
                    ->get();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Saran fasilitas IVA setelah skrining',
                'data' => [
                    'recommendation_text' => 'Berdasarkan hasil skrining, kami merekomendasikan Anda untuk melakukan tes IVA di fasilitas kesehatan berikut:',
                    'facilities' => $suggestions,
                    'total_suggestions' => $suggestions->count(),
                    'note' => 'Silakan hubungi fasilitas untuk membuat janji temu sebelum berkunjung.'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil saran fasilitas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}