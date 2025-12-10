<?php

namespace App\Http\Controllers;

use App\Models\CulturalHeritage;
use Illuminate\Http\Request;

class CulturalHeritageController extends Controller
{
    /**
     * Display the cultural heritage page
     */
    public function index(Request $request)
    {
        $query = CulturalHeritage::published()->ordered();

        // Filter by category if specified
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $heritages = $query->get();
        
        // Get featured/main content - prioritize "Evelyn Otong Hamja"
        $featured = CulturalHeritage::published()
            ->where('slug', 'evelyn-otong-hamja')
            ->first();
        
        // If Evelyn Otong Hamja doesn't exist or isn't published, use first ordered item
        if (!$featured) {
            $featured = $heritages->first();
        }
        
        // Get categories with counts
        $categories = CulturalHeritage::published()
            ->select('category', \DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        return view('cultural-heritage.index', compact('heritages', 'featured', 'categories'));
    }

    /**
     * Display a specific heritage content
     */
    public function show($slug)
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

        return view('cultural-heritage.show', compact('heritage', 'related'));
    }
}
