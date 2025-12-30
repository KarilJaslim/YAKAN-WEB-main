@extends('layouts.app')

@section('title', $pattern->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-maroon-700 to-maroon-800 shadow-2xl" style="background: linear-gradient(to right, #800000, #600000);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-white">{{ $pattern->name }}</h1>
                    <p class="text-maroon-100 mt-2">{{ $pattern->category }} • {{ ucfirst($pattern->difficulty_level) }} Difficulty</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full {{ $pattern->difficulty_level == 'simple' ? 'bg-green-100 text-green-800' : ($pattern->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($pattern->difficulty_level) }}
                    </span>
                    @if($pattern->is_active)
                        <span class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800">Available</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Gallery -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    @if($pattern->media->isNotEmpty())
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($pattern->media as $media)
                                <div>
                                    <img src="{{ $media->url }}" alt="{{ $media->alt_text ?? $pattern->name }}" class="w-full h-96 object-cover" />
                                    @if($media->alt_text)
                                        <p class="text-sm text-gray-600 mt-2 italic">{{ $media->alt_text }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                @if($pattern->description)
                    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                        <h2 class="text-xl font-black text-gray-900 mb-4">About This Pattern</h2>
                        <div class="prose text-gray-700">{{ $pattern->description }}</div>
                    </div>
                @endif

                <!-- Tags -->
                @if($pattern->tags->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                        <h2 class="text-xl font-black text-gray-900 mb-4">Tags</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($pattern->tags as $tag)
                                <span class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full bg-maroon-100 text-maroon-800">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-black text-gray-900 mb-4">Pattern Details</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-gray-900 font-semibold">{{ ucfirst($pattern->category) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Difficulty Level</dt>
                            <dd class="text-gray-900 font-semibold">{{ ucfirst($pattern->difficulty_level) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estimated Time</dt>
                            <dd class="text-gray-900 font-semibold">{{ $pattern->estimated_days }} days</dd>
                        </div>
                        @if($pattern->base_color)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Base Color</dt>
                                <dd class="text-gray-900 font-semibold">{{ $pattern->base_color }}</dd>
                            </div>
                        @endif
                        @if($pattern->base_price_multiplier)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price Multiplier</dt>
                                <dd class="text-gray-900 font-semibold">{{ $pattern->base_price_multiplier }}x</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-black text-gray-900 mb-4">Use This Pattern</h2>
                    <div class="space-y-3">
                        <a href="{{ route('custom_orders.create') }}?pattern_id={{ $pattern->id }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-maroon-600 text-white font-black rounded-lg hover:bg-maroon-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Create Custom Order
                        </a>
                        <button id="wishlistBtn" 
                                class="w-full border-2 border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300 flex items-center justify-center gap-2"
                                onclick="toggleWishlist('pattern', {{ $pattern->id }})"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span id="wishlistBtnText">Add to Wishlist</span>
                        </button>
                        <button onclick="window.history.back()" class="w-full px-6 py-3 bg-gray-200 text-gray-700 font-black rounded-lg hover:bg-gray-300 transition-colors">Back to Gallery</button>
                    </div>
                </div>

                <!-- Related Patterns -->
                @if(isset($relatedPatterns) && $relatedPatterns->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-black text-gray-900 mb-4">Related Patterns</h2>
                        <div class="space-y-3">
                            @foreach($relatedPatterns->take(3) as $related)
                                <a href="{{ route('patterns.show', $related) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    @if($related->media->isNotEmpty())
                                        <img src="{{ $related->media->first()->url }}" alt="{{ $related->name }}" class="w-12 h-12 object-cover rounded-lg" />
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-black text-gray-900 truncate">{{ $related->name }}</h4>
                                        <p class="text-xs text-gray-600">{{ ucfirst($related->difficulty_level) }} • {{ ucfirst($related->category) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Views -->
                @include('layouts._recent_views')
            </div>
        </div>
    </div>
</div>

<script>
// Wishlist functionality
function checkWishlistStatus() {
    fetch('{{ route("wishlist.check") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: 'pattern',
            id: {{ $pattern->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        updateWishlistButton(data.in_wishlist);
    })
    .catch(error => console.error('Error checking wishlist:', error));
}

function toggleWishlist(type, id) {
    const btn = document.getElementById('wishlistBtn');
    const btnText = document.getElementById('wishlistBtnText');
    
    // Disable button temporarily
    btn.disabled = true;
    btnText.textContent = 'Loading...';
    
    const action = btn.classList.contains('in-wishlist') ? 'remove' : 'add';
    const route = action === 'add' ? '{{ route("wishlist.add") }}' : '{{ route("wishlist.remove") }}';
    
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: type,
            id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateWishlistButton(action === 'add');
            showNotification(data.message);
        } else {
            showNotification(data.message || 'Error occurred', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating wishlist:', error);
        showNotification('Error updating wishlist', 'error');
    })
    .finally(() => {
        btn.disabled = false;
    });
}

function updateWishlistButton(inWishlist) {
    const btn = document.getElementById('wishlistBtn');
    const btnText = document.getElementById('wishlistBtnText');
    
    if (inWishlist) {
        btn.classList.add('in-wishlist', 'border-red-500', 'text-red-600', 'bg-red-50');
        btn.classList.remove('border-gray-300', 'text-gray-700');
        btnText.textContent = 'Remove from Wishlist';
    } else {
        btn.classList.remove('in-wishlist', 'border-red-500', 'text-red-600', 'bg-red-50');
        btn.classList.add('border-gray-300', 'text-gray-700');
        btnText.textContent = 'Add to Wishlist';
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatus();
});
</script>
@endsection
