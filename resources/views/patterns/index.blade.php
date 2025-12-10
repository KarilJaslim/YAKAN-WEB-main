@extends('layouts.app')

@section('title', 'Cultural Patterns Archive')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-maroon-700 to-maroon-800 shadow-2xl" style="background: linear-gradient(to right, #800000, #600000);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-black text-white mb-4">Yakan Cultural Patterns</h1>
                <p class="text-xl text-maroon-100 max-w-2xl mx-auto">Explore our curated collection of traditional and contemporary Yakan weaving patterns, each telling a story of heritage and artistry.</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500">
                        <option value="">All Categories</option>
                        <option value="traditional" {{ request('category') == 'traditional' ? 'selected' : '' }}>Traditional</option>
                        <option value="modern" {{ request('category') == 'modern' ? 'selected' : '' }}>Modern</option>
                        <option value="contemporary" {{ request('category') == 'contemporary' ? 'selected' : '' }}>Contemporary</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                    <select name="difficulty" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500">
                        <option value="">All Levels</option>
                        <option value="simple" {{ request('difficulty') == 'simple' ? 'selected' : '' }}>Simple</option>
                        <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="complex" {{ request('complex') == 'complex' ? 'selected' : '' }}>Complex</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tag</label>
                    <select name="tag" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500">
                        <option value="">All Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->slug }}" {{ request('tag') == $tag->slug ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-maroon-600 text-white rounded-lg hover:bg-maroon-700 transition-colors">Filter</button>
                <a href="{{ route('patterns.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Reset</a>
            </form>
        </div>
    </div>

    <!-- Patterns Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        @if($patterns->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($patterns as $pattern)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        @if($pattern->media->isNotEmpty())
                            <a href="{{ route('patterns.show', $pattern) }}">
                                <img src="{{ $pattern->media->first()->url }}" alt="{{ $pattern->media->first()->alt_text ?? $pattern->name }}" class="w-full h-56 object-cover" />
                            </a>
                        @else
                            <a href="{{ route('patterns.show', $pattern) }}" class="block w-full h-56 bg-gray-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </a>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-black text-gray-900 truncate">{{ $pattern->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $pattern->category }}</p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($pattern->tags->take(3) as $tag)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-full bg-maroon-100 text-maroon-800">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full {{ $pattern->difficulty_level == 'simple' ? 'bg-green-100 text-green-800' : ($pattern->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($pattern->difficulty_level) }}
                                </span>
                                <a href="{{ route('patterns.show', $pattern) }}" class="text-maroon-600 hover:text-maroon-800 text-sm font-semibold">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $patterns->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-black text-gray-900 mb-2">No Patterns Found</h3>
                <p class="text-gray-600">Try adjusting your filters or check back later for new patterns.</p>
            </div>
        @endif
    </div>
</div>
@endsection
