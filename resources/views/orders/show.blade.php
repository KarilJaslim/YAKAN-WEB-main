@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm animate-pulse">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">Order Details</h1>
                        <p class="text-gray-600 mt-1">Track your order status and view purchase details</p>
                    </div>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>

        @if($order->tracking_status === 'Out for Delivery')
            <div class="mb-6 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-4 flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-500 text-white flex-shrink-0">
                    <span class="text-xl">🚛</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900">Your order is out for delivery</p>
                    @if($order->delivery_address)
                        <p class="text-sm text-blue-800 mt-1">It's on the way to: <span class="font-medium">{{ $order->delivery_address }}</span></p>
                    @endif
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Order Status Card -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-white">
                                <h2 class="text-2xl font-bold mb-1">Order #{{ $order->id }}</h2>
                                <p class="text-red-100">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            @php
                                $statusConfig = [
                                    'pending' => ['bg-yellow-100 text-yellow-800', '⏳', 'Pending'],
                                    'processing' => ['bg-blue-100 text-blue-800', '⚙️', 'Processing'],
                                    'shipped' => ['bg-purple-100 text-purple-800', '🚚', 'Shipped'],
                                    'delivered' => ['bg-green-100 text-green-800', '✅', 'Delivered'],
                                    'completed' => ['bg-green-100 text-green-800', '🎉', 'Order Received'],
                                    'cancelled' => ['bg-red-100 text-red-800', '❌', 'Cancelled'],
                                ];
                                $statusInfo = $statusConfig[$order->status] ?? ['bg-gray-100 text-gray-800', '📦', 'Unknown'];
                            @endphp
                            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                                <span class="text-white font-semibold">{{ $statusInfo[1] }} {{ $statusInfo[2] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">₱{{ number_format($order->total_amount, 0) }}</div>
                                <div class="text-sm text-gray-500">Total Amount</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $order->orderItems->sum('quantity') }}</div>
                                <div class="text-sm text-gray-500">Items</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $order->payment_method === 'online' ? 'GCash' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                                <div class="text-sm text-gray-500">Payment</div>
                            </div>
                            <div class="text-center">
                                <div class="px-3 py-1 {{ $statusInfo[0] }} rounded-full text-sm font-bold">
                                    {{ $statusInfo[2] }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Status</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(in_array($order->status, ['shipped', 'delivered']) && ($order->courier_name || $order->courier_tracking_url || $order->courier_contact || $order->estimated_delivery_date))
                    <!-- Shipping Information -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Shipping Information
                                </h2>
                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-100 text-blue-800 uppercase tracking-wide">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 space-y-3 text-sm text-gray-700">
                            @if($order->courier_name)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Courier</span>
                                    <span class="font-semibold text-gray-900">{{ $order->courier_name }}</span>
                                </div>
                            @endif

                            @if($order->tracking_number)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Tracking Number</span>
                                    <div class="text-right">
                                        <span class="font-semibold text-gray-900">{{ $order->tracking_number }}</span>
                                        <div>
                                            <a href="{{ route('track-order.show', ['trackingNumber' => $order->tracking_number]) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View tracking details →</a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($order->courier_tracking_url)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Courier Tracking</span>
                                    <a href="{{ $order->courier_tracking_url }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Open courier tracking →</a>
                                </div>
                            @endif

                            @if($order->courier_contact)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Courier Contact</span>
                                    <span class="font-semibold text-gray-900">{{ $order->courier_contact }}</span>
                                </div>
                            @endif

                            @if($order->estimated_delivery_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Estimated Delivery</span>
                                    <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($order->estimated_delivery_date)->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Order Items -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Order Items</h2>
                            <span class="text-sm text-gray-500">{{ $order->orderItems->count() }} items</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-6">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-start gap-6 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors duration-200">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-24 h-24 bg-gradient-to-br from-red-100 to-red-200 rounded-2xl flex items-center justify-center shadow-sm">
                                        @if($item->product->image)
                                            <img src="{{ asset('uploads/products/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded-2xl">
                                        @else
                                            <svg class="w-12 h-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $item->product->name }}</h3>
                                        @if($item->product->category)
                                            <p class="text-sm text-gray-500 mb-3">{{ $item->product->category->name }}</p>
                                        @endif
                                        <div class="flex flex-wrap items-center gap-4 text-sm">
                                            <div class="flex items-center text-gray-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                                </svg>
                                                <span>Quantity: <strong>{{ $item->quantity }}</strong></span>
                                            </div>
                                            <div class="flex items-center text-gray-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Price: <strong>₱{{ number_format($item->price, 2) }}</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="flex-shrink-0 text-right">
                                        <div class="text-2xl font-bold text-red-600">₱{{ number_format($item->price * $item->quantity, 2) }}</div>
                                        <div class="text-sm text-gray-500">Subtotal</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Customer Notes Section -->
                @if($order->customer_notes)
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <h2 class="text-xl font-bold text-gray-900">Your Order Notes</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                <p class="text-gray-700 leading-relaxed">{{ $order->customer_notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Payment Status Info -->
                @if(
                    ($order->payment_method === 'online' || $order->payment_method === 'bank_transfer')
                    && $order->payment_status === 'pending'
                    && !in_array($order->status, ['shipped', 'delivered'])
                )
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-3xl p-6 border border-blue-200">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 bg-blue-600">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold mb-3 text-lg text-blue-900">Complete Your Payment</h3>
                                <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-4">
                                    <p class="mb-3 text-blue-800">
                                        @if($order->payment_method === 'online')
                                            Please submit your GCash payment proof to start processing your order. Payment will be verified automatically!
                                        @else
                                            Please upload your bank transfer receipt to start processing your order. Payment will be verified automatically!
                                        @endif
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-blue-800">Verification time:</p>
                                            <p class="font-bold text-blue-900">Instant (automated)</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                Awaiting Payment
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Tracking -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Order Tracking</h2>
                            <span class="text-sm text-gray-500">{{ $order->tracking_history ? count($order->tracking_history) : 1 }} updates</span>
                        </div>
                    </div>
                    
                    <!-- Tracking Number Display -->
                    <div class="px-6 pt-6">
                        <div class="rounded-2xl p-6 mb-6" style="background: linear-gradient(135deg, rgba(128, 0, 0, 0.05) 0%, rgba(128, 0, 0, 0.1) 100%);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mr-4" style="background-color: #800000;">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Your Tracking Number</p>
                                        <p class="text-2xl font-bold font-mono" style="color: #800000;">{{ $order->tracking_number }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('track-order.show', $order->tracking_number) }}" 
                                   class="px-6 py-3 text-white rounded-xl hover:opacity-90 transition-opacity font-semibold flex items-center"
                                   style="background-color: #800000;">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Track Order
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 pt-0">
                        @if($order->tracking_history && count($order->tracking_history) > 0)
                            <div class="relative">
                                @php
                                    $trackingConfig = [
                                        'Order Placed'     => ['bg-yellow-500', '📝', 'Your order has been placed successfully'],
                                        'Processing'       => ['bg-blue-500',   '⚙️', 'Your order is being prepared'],
                                        'Packed'           => ['bg-indigo-500', '📦', 'Your order has been packed and is waiting for pickup'],
                                        'Shipped'          => ['bg-purple-500', '🚚', 'Your order is on the way to your area'],
                                        'Out for Delivery' => ['bg-orange-500', '🚛', 'Your order is out for delivery and will arrive soon'],
                                        'Delivered'        => ['bg-green-500',  '✅', 'Your order has been delivered'],
                                        'Cancelled'        => ['bg-red-500',    '❌', 'Your order has been cancelled'],
                                    ];
                                @endphp
                                
                                @foreach($order->tracking_history as $index => $history)
                                    @php
                                        $config = $trackingConfig[$history['status']] ?? ['bg-gray-500', '📦', 'Status updated'];
                                        $isLast = $index === count($order->tracking_history) - 1;
                                    @endphp
                                    <div class="flex items-start mb-8 {{ !$isLast ? 'pb-8' : '' }}">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 {{ $config[0] }} rounded-full flex items-center justify-center ring-4 ring-white shadow-lg">
                                                <span class="text-white text-xl">{{ $config[1] }}</span>
                                            </div>
                                            @if(!$isLast)
                                                <div class="w-0.5 h-16 bg-gray-200 ml-6 mt-2"></div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="bg-gray-50 rounded-2xl p-4">
                                                <h3 class="font-bold text-gray-900 mb-1">{{ $history['status'] }}</h3>
                                                <p class="text-sm text-gray-600 mb-2">{{ $config[2] }}</p>
                                                <time class="text-xs text-gray-500 font-medium">{{ $history['date'] }}</time>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tracking Not Available</h3>
                                <p class="text-gray-500">Tracking information will be available once your order is processed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <!-- Order Summary Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6">
                            <h2 class="text-xl font-bold text-white mb-1">Order Summary</h2>
                            <p class="text-red-100">Order #{{ $order->id }}</p>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Order Date</span>
                                    <span class="text-gray-900 font-semibold">{{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Order Time</span>
                                    <span class="text-gray-900 font-semibold">{{ $order->created_at->format('h:i A') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Delivery Option</span>
                                    <span class="text-gray-900 font-semibold">{{ $order->delivery_type === 'pickup' ? 'Store Pickup' : 'Delivery' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Payment Method</span>
                                    <span class="text-gray-900 font-semibold">
                                        @if($order->payment_method === 'online')
                                            GCash
                                        @elseif($order->payment_method === 'bank_transfer')
                                            Bank Transfer
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                        @endif
                                    </span>
                                </div>
                                @php
                                    $paymentStatusConfig = [
                                        'paid' => ['bg' => 'bg-green-100 text-green-800', 'label' => 'Paid'],
                                        'pending' => ['bg' => 'bg-yellow-100 text-yellow-800', 'label' => 'Pending Verification'],
                                        'refunded' => ['bg' => 'bg-purple-100 text-purple-800', 'label' => 'Refunded'],
                                    ];
                                    $paymentStatus = $paymentStatusConfig[$order->payment_status] ?? ['bg' => 'bg-red-100 text-red-800', 'label' => 'Unpaid'];
                                @endphp
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Payment Status</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $paymentStatus['bg'] }}">
                                        {{ $paymentStatus['label'] }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-600 font-medium">Status</span>
                                    <span class="px-3 py-1 {{ $statusInfo[0] }} rounded-full text-sm font-bold">
                                        {{ $statusInfo[2] }}
                                    </span>
                                </div>
                            </div>

                            <div class="border-t-2 border-gray-200 pt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-lg font-bold text-gray-900">Total Amount</span>
                                    <span class="text-3xl font-bold text-red-600">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <p class="text-sm text-gray-500">Including all taxes and fees</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('products.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-4 rounded-2xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg font-semibold">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                Continue Shopping
                            </a>
                            
                            <a href="{{ route('dashboard') }}" 
                               class="block w-full text-center text-gray-700 hover:text-red-600 px-6 py-4 rounded-2xl border-2 border-gray-200 hover:border-red-600 transition-all duration-200 font-semibold">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Go to Dashboard
                            </a>

                            @if($order->status === 'delivered')
                                <form action="{{ route('orders.confirm-received', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-center bg-green-600 text-white px-6 py-4 rounded-2xl hover:bg-green-700 transition-all duration-200 font-semibold mb-3">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Confirm Order Received
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'completed')
                                <div class="block w-full text-center bg-green-100 text-green-800 px-6 py-4 rounded-2xl border-2 border-green-200 font-semibold mb-3">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Order Received & Completed
                                </div>
                            @endif

                            @if(in_array($order->status, ['delivered', 'completed']))
                                <button onclick="showReorderModal()" class="block w-full text-center bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all duration-200 font-semibold">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Reorder Items
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Support Card -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-3xl p-6 border border-gray-200">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2">Need Help?</h3>
                            <p class="text-sm text-gray-600 mb-4">Our support team is here to assist you with any questions about your order.</p>
                            <div class="space-y-2">
                                <a href="mailto:support@yakan.com" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    support@yakan.com
                                </a>
                                <div class="text-sm text-gray-500">or</div>
                                <a href="tel:+639123456789" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    +63 912 345 6789
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reorder Modal -->
<div id="reorderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Reorder Items</h3>
            <p class="text-gray-600 mb-6">Would you like to add all items from this order to your cart?</p>
            <div class="flex space-x-4">
                <button onclick="closeReorderModal()" class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 transition-colors font-semibold">
                    Cancel
                </button>
                <button onclick="reorderItems()" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors font-semibold">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showReorderModal() {
    document.getElementById('reorderModal').classList.remove('hidden');
}

function closeReorderModal() {
    document.getElementById('reorderModal').classList.add('hidden');
}

function reorderItems() {
    // Add reorder functionality here
    const orderItems = @json($order->orderItems->map(function($item) {
        return [
            'id' => $item->product_id,
            'quantity' => $item->quantity,
            'name' => $item->product->name
        ];
    }));
    
    // Add items to cart logic
    console.log('Reordering items:', orderItems);
    
    // Show success message
    alert('Items added to cart successfully!');
    closeReorderModal();
    
    // Redirect to cart
    window.location.href = '{{ route('cart.index') }}';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reorderModal');
    if (event.target === modal) {
        closeReorderModal();
    }
}
</script>
@endsection
