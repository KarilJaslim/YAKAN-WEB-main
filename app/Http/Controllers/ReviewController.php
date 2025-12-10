<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        // Check if user can review this product
        if (!$product->canBeReviewedBy(auth()->user())) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $review = new Review($validated);
        $review->user_id = auth()->id();
        $review->product_id = $product->id;
        $review->is_verified = true; // Auto-verify for now
        $review->save();

        return back()->with('success', 'Your review has been submitted successfully!');
    }

    /**
     * Show the review creation form.
     */
    public function create(Product $product): View
    {
        if (!$product->canBeReviewedBy(auth()->user())) {
            abort(403, 'You have already reviewed this product.');
        }

        return view('reviews.create', compact('product'));
    }

    /**
     * Show the review editing form.
     */
    public function edit(Review $review): View
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $product = $review->product;
        return view('reviews.edit', compact('review', 'product'));
    }

    /**
     * Mark a review as helpful.
     */
    public function helpful(Review $review): RedirectResponse
    {
        $review->markAsHelpful();
        return back()->with('success', 'Review marked as helpful!');
    }

    /**
     * Update a review.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $review->update($validated);

        return back()->with('success', 'Review updated successfully!');
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }

    /**
     * Show all reviews for a product.
     */
    public function index(Product $product, Request $request): View
    {
        $reviews = $product->reviews()
            ->with('user')
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
            ->paginate(10);

        return view('reviews.index', compact('product', 'reviews'));
    }
}
