<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Get all reviews for a product.
     */
    public function index(Product $product, Request $request): JsonResponse
    {
        $reviews = $product->reviews()
            ->with('user:id,name,email')
            ->when($request->rating, function ($query, $rating) {
                return $query->where('rating', $rating);
            })
            ->when($request->sort, function ($query, $sort) {
                if ($sort === 'newest') {
                    return $query->latest();
                } elseif ($sort === 'oldest') {
                    return $query->oldest();
                } elseif ($sort === 'highest') {
                    return $query->orderBy('rating', 'desc');
                } elseif ($sort === 'lowest') {
                    return $query->orderBy('rating', 'asc');
                } elseif ($sort === 'helpful') {
                    return $query->orderBy('helpful_count', 'desc');
                }
                return $query->latest();
            })
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Reviews retrieved successfully'
        ]);
    }

    /**
     * Store a new review.
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can review this product
        if (!$product->canBeReviewedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product or have not purchased it.'
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $review = new Review($validated);
        $review->user_id = $user->id;
        $review->product_id = $product->id;
        $review->is_verified = true; // Auto-verify for now
        $review->save();

        // Load relationships for response
        $review->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Your review has been submitted successfully!'
        ], 201);
    }

    /**
     * Show a specific review.
     */
    public function show(Review $review): JsonResponse
    {
        $review->load(['user:id,name,email', 'product:id,name']);
        
        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Review retrieved successfully'
        ]);
    }

    /**
     * Update a review.
     */
    public function update(Request $request, Review $review): JsonResponse
    {
        $user = Auth::user();
        
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $review->update($validated);
        $review->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Review updated successfully!'
        ]);
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review): JsonResponse
    {
        $user = Auth::user();
        
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully!'
        ]);
    }

    /**
     * Mark a review as helpful.
     */
    public function helpful(Review $review): JsonResponse
    {
        $review->markAsHelpful();
        
        return response()->json([
            'success' => true,
            'data' => [
                'helpful_count' => $review->helpful_count
            ],
            'message' => 'Review marked as helpful!'
        ]);
    }

    /**
     * Get user's reviews.
     */
    public function userReviews(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $reviews = $user->reviews()
            ->with('product:id,name,price,image')
            ->when($request->rating, function ($query, $rating) {
                return $query->where('rating', $rating);
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'User reviews retrieved successfully'
        ]);
    }

    /**
     * Get review statistics for a product.
     */
    public function statistics(Product $product): JsonResponse
    {
        $statistics = $product->reviews()
            ->selectRaw('
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'message' => 'Review statistics retrieved successfully'
        ]);
    }
}
