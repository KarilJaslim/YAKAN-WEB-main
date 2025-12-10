@extends('layouts.app')

@section('title', 'Product Reviews')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center text-maroon-600 hover:text-maroon-800 font-bold mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Product
            </a>
            <h1 class="text-3xl font-black text-gray-900">Customer Reviews</h1>
            <p class="text-gray-600 mt-2">What people are saying about {{ $product->name }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Rating Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                    <!-- Overall Rating -->
                    <div class="text-center mb-6">
                        <div class="text-5xl font-black text-gray-900">{{ number_format($product->average_rating, 1) }}</div>
                        <div class="flex justify-center space-x-1 my-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($product->average_rating))
                                    <i class="fas fa-star text-yellow-400 text-xl"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-xl"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="text-gray-600 font-medium">{{ $product->review_count }} Reviews</div>
                    </div>

                    <!-- Rating Breakdown -->
                    <div class="space-y-3">
                        @foreach($product->rating_breakdown as $rating => $data)
                            <div class="flex items-center">
                                <span class="text-sm font-bold text-gray-700 w-12">{{ $rating }}★</span>
                                <div class="flex-1 mx-3">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 w-12 text-right">{{ $data['count'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Write Review Button -->
                    @if(auth()->check() && $product->can_be_reviewed_by(auth()->user()))
                        <a href="{{ route('reviews.create', $product) }}" class="w-full mt-6 inline-flex items-center justify-center px-4 py-3 bg-maroon-600 hover:bg-maroon-700 text-white font-black rounded-lg shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Write a Review
                        </a>
                    @elseif(auth()->check())
                        <div class="w-full mt-4 p-3 bg-gray-100 rounded-lg text-center">
                            <p class="text-sm text-gray-600 font-medium">You've already reviewed this product</p>
                        </div>
                    @else
                        <a href="{{ route('login.user.form') }}" class="w-full mt-6 inline-flex items-center justify-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login to Review
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Column - Reviews List -->
            <div class="lg:col-span-2">
                <!-- Filters and Sort -->
                <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-black text-gray-700 mb-2">Filter by Rating</label>
                            <select onchange="window.location.href='?rating='+this.value+'&sort={{ request('sort', 'newest') }}'" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                <option value="">All Ratings</option>
                                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-black text-gray-700 mb-2">Sort By</label>
                            <select onchange="window.location.href='?rating={{ request('rating') }}&sort='+this.value" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Highest Rating</option>
                                <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Lowest Rating</option>
                                <option value="helpful" {{ request('sort') == 'helpful' ? 'selected' : '' }}>Most Helpful</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    @forelse($reviews as $review)
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <!-- Review Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-maroon-100 flex items-center justify-center text-maroon-600 font-black text-lg">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-black text-gray-900">{{ $review->user->name }}</div>
                                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                                            <div class="flex">
                                                {!! $review->rating_stars !!}
                                            </div>
                                            <span>{{ $review->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($review->is_verified)
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-black rounded-full">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-1 {{ $review->status_color }} text-xs font-black rounded-full">
                                        {{ $review->status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Review Title -->
                            @if($review->title)
                                <h3 class="text-lg font-black text-gray-900 mb-2">{{ $review->title }}</h3>
                            @endif

                            <!-- Review Comment -->
                            <div class="text-gray-700 mb-4">{{ $review->comment }}</div>

                            <!-- Admin Response -->
                            @if($review->admin_response)
                                <div class="bg-maroon-50 border border-maroon-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center mb-2">
                                        <span class="text-sm font-black text-maroon-800">Seller Response</span>
                                        <span class="text-xs text-maroon-600 ml-2">{{ $review->admin_response_at->format('M d, Y') }}</span>
                                    </div>
                                    <p class="text-sm text-maroon-700">{{ $review->admin_response }}</p>
                                </div>
                            @endif

                            <!-- Review Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <form method="POST" action="{{ route('reviews.helpful', $review) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center text-sm text-gray-600 hover:text-maroon-600 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                            Helpful ({{ $review->helpful_count }})
                                        </button>
                                    </form>
                                </div>
                                @if(auth()->check() && $review->user_id == auth()->id())
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('reviews.edit', $review) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                        <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <h3 class="text-lg font-black text-gray-900 mb-2">No Reviews Yet</h3>
                            <p class="text-gray-600 mb-4">Be the first to share your experience with this product!</p>
                            @if(auth()->check() && $product->can_be_reviewed_by(auth()->user()))
                                <a href="{{ route('reviews.create', $product) }}" class="inline-flex items-center px-4 py-2 bg-maroon-600 hover:bg-maroon-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                    Write First Review
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($reviews->hasPages())
                    <div class="mt-8">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
