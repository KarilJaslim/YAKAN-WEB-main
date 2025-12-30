<?php

namespace App\Http\Controllers;

use App\Models\YakanPattern;
use App\Models\PatternTag;
use Illuminate\Http\Request;

class PatternController extends Controller
{
    public function index(Request $request)
    {
        $query = YakanPattern::active()->with('media', 'tags');

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $patterns = $query->latest()->paginate(16);
        $tags = PatternTag::all();

        return view('patterns.index', compact('patterns', 'tags'));
    }

    public function show(YakanPattern $pattern)
    {
        if (!$pattern->is_active) {
            abort(404);
        }

        $pattern->load('media', 'tags');
        $pattern->incrementPopularity();

        // Track recent view
        if (auth()->check()) {
            \App\Models\RecentView::track($pattern, auth()->id());
        }

        $relatedPatterns = YakanPattern::active()
            ->where('id', '!=', $pattern->id)
            ->where('category', $pattern->category)
            ->with('media')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('patterns.show', compact('pattern', 'relatedPatterns'));
    }
}
