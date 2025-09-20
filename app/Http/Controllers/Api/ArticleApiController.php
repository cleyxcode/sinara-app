<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleApiController extends Controller
{
   /**
 * Get all active facilities
 */
public function index(Request $request): JsonResponse
{
    try {
        $query = FacilityIva::active();

        // Filter by location HANYA jika location parameter benar-benar dikirim dan tidak kosong
        $location = $request->get('location');
        if (!empty($location) && $location !== 'all' && $location !== null) {
            $query->byLocation($location);
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 15);
        
        // Pastikan per_page tidak terlalu kecil
        if ($perPage < 1) $perPage = 15;
        if ($perPage > 100) $perPage = 100;

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
            ],
            'filter_applied' => [
                'location' => $location ?? null,
                'is_filtered' => !empty($location) && $location !== 'all'
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

    public function show($id): JsonResponse
    {
        try {
            $article = Article::published()
                ->select('id', 'title', 'content', 'image', 'created_at', 'updated_at')
                ->findOrFail($id);

            $data = [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'excerpt' => $this->generateExcerpt($article->content),
                'image' => $article->image,
                'image_url' => $article->image_url,
                'created_at' => $article->created_at,
                'updated_at' => $article->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Article retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLatest(): JsonResponse
    {
        try {
            $articles = Article::published()
                ->select('id', 'title', 'content', 'image', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $data = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'excerpt' => $this->generateExcerpt($article->content),
                    'image' => $article->image,
                    'image_url' => $article->image_url,
                    'created_at' => $article->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Latest articles retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve latest articles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $keyword = $request->get('q', '');
            $perPage = $request->get('per_page', 10);

            if (empty($keyword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keyword is required'
                ], 400);
            }

            $articles = Article::published()
                ->select('id', 'title', 'content', 'image', 'created_at', 'updated_at')
                ->where(function ($query) use ($keyword) {
                    $query->where('title', 'LIKE', "%{$keyword}%")
                          ->orWhere('content', 'LIKE', "%{$keyword}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $articles->getCollection()->transform(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => $article->content,
                    'excerpt' => $this->generateExcerpt($article->content),
                    'image' => $article->image,
                    'image_url' => $article->image_url,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Search results retrieved successfully',
                'data' => $articles->items(),
                'meta' => [
                    'current_page' => $articles->currentPage(),
                    'total' => $articles->total(),
                    'per_page' => $articles->perPage(),
                    'last_page' => $articles->lastPage(),
                    'keyword' => $keyword,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStats(): JsonResponse
    {
        try {
            $totalArticles = Article::published()->count();
            $totalDrafts = Article::where('is_published', false)->count();
            $thisMonthArticles = Article::published()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $stats = [
                'total_published' => $totalArticles,
                'total_drafts' => $totalDrafts,
                'total_all' => $totalArticles + $totalDrafts,
                'this_month' => $thisMonthArticles,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Article statistics retrieved successfully',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateExcerpt($content, $length = 150): string
    {
        $plainText = strip_tags($content);
        
        if (strlen($plainText) <= $length) {
            return trim($plainText);
        }
        
        $excerpt = substr($plainText, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');
        
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return trim($excerpt) . '...';
    }
}