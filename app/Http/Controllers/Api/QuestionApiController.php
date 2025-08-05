<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionCollection;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionApiController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $questions = Question::active()
                ->ordered()
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Questions retrieved successfully',
                'data' => QuestionResource::collection($questions),
                'total' => $questions->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve questions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getByCategory(string $category): JsonResponse
    {
        try {
            $category = urldecode($category);
            
            if (!array_key_exists($category, Question::CATEGORIES)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid category',
                    'available_categories' => array_keys(Question::CATEGORIES)
                ], 400);
            }

            $questions = Question::active()
                ->where('category', $category)
                ->ordered()
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => "Questions for category '{$category}' retrieved successfully",
                'data' => QuestionResource::collection($questions),
                'total' => $questions->count(),
                'category' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve questions by category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $question = Question::active()->find($id);

            if (!$question) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Question not found or inactive'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Question retrieved successfully',
                'data' => new QuestionResource($question)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve question',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getCategories(): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Categories retrieved successfully',
                'data' => Question::CATEGORIES
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getGroupedByCategory(): JsonResponse
    {
        try {
            $questions = Question::active()
                ->ordered()
                ->get()
                ->groupBy('category');

            $groupedData = [];
            foreach ($questions as $category => $categoryQuestions) {
                $groupedData[] = [
                    'category' => $category,
                    'total_questions' => $categoryQuestions->count(),
                    'questions' => QuestionResource::collection($categoryQuestions)->resolve()
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Grouped questions retrieved successfully',
                'data' => $groupedData,
                'total_categories' => count($groupedData),
                'total_questions' => Question::active()->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve grouped questions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getStats(): JsonResponse
    {
        try {
            $totalQuestions = Question::active()->count();
            $questionsByCategory = Question::active()
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category');

            return response()->json([
                'status' => 'success',
                'message' => 'Questions statistics retrieved successfully',
                'data' => [
                    'total_active_questions' => $totalQuestions,
                    'questions_by_category' => $questionsByCategory,
                    'available_categories' => array_keys(Question::CATEGORIES)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve questions statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}