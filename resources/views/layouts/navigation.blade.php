<nav x-data="{ open: false, accountOpen: false }" 
     class="fixed top-0 left-0 right-0 z-50 bg-red-600 shadow-lg border-b border-red-700">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Left Section: Logo & Navigation -->
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-all duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">YAKAN</span>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">Dashboard</a>
                    <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">Products</a>
                    <a href="{{ route('patterns.index') }}" class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">Patterns</a>
                    <a href="{{ route('orders.index') }}" class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">Orders</a>
                    <a href="{{ route('wishlist.index') }}" class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">Wishlist</a>
                    <a href="{{ route('custom_orders.index') }}" class="px-4 py-2 text-sm font-medium bg-white text-red-600 hover:bg-red-50 rounded-lg transition-all shadow-md">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Design Order
                    </a>
                    <a href="{{ route('custom_orders.index') }}" class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">My Orders</a>
                </div>
            </div>

            <!-- Right Section: Cart, Notifications & Account -->
            <div class="flex items-center space-x-2">

                <!-- Cart Icon -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-yellow-400 text-red-600 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <!-- Notifications -->
                <button class="relative p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-yellow-400 rounded-full"></span>
                </button>

                <!-- Account Dropdown -->
                <div class="relative" x-data="{ show: false }" @click.away="show = false">
                    <button @click="show = !show" type="button" class="flex items-center gap-2 px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden lg:block text-left">
                            <div class="text-sm font-semibold text-white">{{ explode(' ', Auth::user()->name)[0] }}</div>
                            <div class="text-xs text-white/70">View Account</div>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': show }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden"
                         style="display: none;">
                        <!-- User info and links -->
                        <div class="px-6 py-4 bg-gradient-to-br from-red-50 to-red-100 border-b border-red-200">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-14 h-14 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-xl ring-4 ring-red-100">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-900 truncate text-base">{{ Auth::user()->name }}</div>
                                    <div class="text-sm text-gray-600 truncate">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-full">
                                {{ ucfirst(Auth::user()->role ?? 'User') }}
                            </span>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('dashboard') }}" class="block px-6 py-3 text-gray-700 hover:bg-red-50 hover:text-red-800">Dashboard</a>
                            <a href="{{ route('orders.index') }}" class="block px-6 py-3 text-gray-700 hover:bg-red-50 hover:text-red-800">Orders</a>
                            <a href="{{ route('custom_orders.create') }}" class="block px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 font-semibold">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Create Custom Order
                            </a>
                            <a href="{{ route('custom_orders.index') }}" class="block px-6 py-3 text-gray-700 hover:bg-red-50 hover:text-red-800">My Custom Orders</a>
                            <a href="{{ route('cart.index') }}" class="block px-6 py-3 text-gray-700 hover:bg-red-50 hover:text-red-800">Shopping Cart</a>
                            <a href="{{ route('profile.edit') }}" class="block px-6 py-3 text-gray-700 hover:bg-red-50 hover:text-red-800">Account Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-6 py-3 text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="open = !open" type="button" class="md:hidden p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-red-700 bg-red-600 shadow-lg"
         style="display: none;">
        <div class="px-4 py-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-white/90 hover:bg-white/10 rounded-lg">Dashboard</a>
            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-white/90 hover:bg-white/10 rounded-lg">Products</a>
            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-white/90 hover:bg-white/10 rounded-lg">Orders</a>
            <a href="{{ route('custom_orders.create') }}" class="block px-4 py-2 bg-white text-red-600 hover:bg-red-50 rounded-lg font-semibold">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Create Order
            </a>
            <a href="{{ route('custom_orders.index') }}" class="block px-4 py-2 text-white/90 hover:bg-white/10 rounded-lg">My Custom Orders</a>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-white/90 hover:bg-white/10 rounded-lg">Settings</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-white/90 hover:bg-white/10 rounded-lg">Logout</button>
            </form>
        </div>
    </div>
</nav>

<!-- Spacer for fixed nav -->
<div class="h-16"></div>
