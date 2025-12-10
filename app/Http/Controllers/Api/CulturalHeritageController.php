<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CulturalHeritage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CulturalHeritageController extends Controller
{
    /**
     * Get all cultural heritage content.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CulturalHeritage::published()->ordered();

        // Filter by category if specified
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $heritages = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'data' => $heritages,
            'message' => 'Cultural heritage content retrieved successfully'
        ]);
    }

    /**
     * Get featured cultural heritage content.
     */
    public function featured(): JsonResponse
    {
        $featured = CulturalHeritage::published()
            ->ordered()
            ->first();

        if (!$featured) {
            return response()->json([
                'success' => false,
                'message' => 'No featured content found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $featured,
            'message' => 'Featured content retrieved successfully'
        ]);
    }

    /**
     * Get specific cultural heritage content by slug.
     */
    public function show($slug): JsonResponse
    {
        $heritage = CulturalHeritage::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Get related content (same category, different item)
        $related = CulturalHeritage::published()
            ->where('category', $heritage->category)
            ->where('id', '!=', $heritage->id)
            ->ordered()
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'heritage' => $heritage,
                'related' => $related
            ],
            'message' => 'Cultural heritage content retrieved successfully'
        ]);
    }

    /**
     * Get cultural heritage categories.
     */
    public function categories(): JsonResponse
    {
        $categories = CulturalHeritage::published()
            ->select('category', \DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Get cultural heritage by category.
     */
    public function byCategory($category, Request $request): JsonResponse
    {
        $heritages = CulturalHeritage::published()
            ->where('category', $category)
            ->ordered()
            ->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'data' => $heritages,
            'message' => "Cultural heritage content for category '{$category}' retrieved successfully"
        ]);
    }

    /**
     * Get cultural heritage statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = CulturalHeritage::published()
            ->selectRaw('
                COUNT(*) as total_content,
                COUNT(DISTINCT category) as total_categories,
                AVG(LENGTH(content)) as avg_content_length
            ')
            ->first();

        $categoryStats = CulturalHeritage::published()
            ->select('category', \DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => $stats,
                'categories' => $categoryStats
            ],
            'message' => 'Cultural heritage statistics retrieved successfully'
        ]);
    }

    /**
     * Search cultural heritage content.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'category' => 'nullable|string',
        ]);

        $query = CulturalHeritage::published()
            ->where(function ($q) use ($request) {
                $search = $request->query;
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });

        // Filter by category if specified
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $results = $query->ordered()->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Search results retrieved successfully'
        ]);
    }

    /**
     * Get recent cultural heritage content.
     */
    public function recent(Request $request): JsonResponse
    {
        $recent = CulturalHeritage::published()
            ->orderBy('created_at', 'desc')
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recent,
            'message' => 'Recent cultural heritage content retrieved successfully'
        ]);
    }
}
