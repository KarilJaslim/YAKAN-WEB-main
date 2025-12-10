@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Payment</h1>
            <p class="text-gray-600">Order #{{ $order->id }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Order Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
            <div class="space-y-3 text-sm">
                <!-- Customer Info -->
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer</span>
                    <span class="font-medium text-gray-900">{{ $order->user->name ?? auth()->user()->name ?? 'N/A' }}</span>
                </div>

                @php
                    $deliveryType = $order->delivery_type ?? ($order->delivery_address ? 'delivery' : 'pickup');
                @endphp

                <div class="flex justify-between">
                    <span class="text-gray-600">Delivery Option</span>
                    <span class="font-medium text-gray-900">
                        {{ $deliveryType === 'pickup' ? 'Store Pickup' : 'Delivery' }}
                    </span>
                </div>

                @if($order->delivery_address)
                    <div>
                        <span class="text-gray-600 text-xs block">Delivery Address</span>
                        <span class="mt-1 font-medium text-gray-900 text-xs whitespace-pre-line block">{{ $order->delivery_address }}</span>
                    </div>
                @endif

                <div class="border-t pt-3 mt-2 space-y-2">
                    <!-- Customized Product Details -->
                    @if(method_exists($order, 'isFabricOrder') && $order->isFabricOrder())
                        <div class="flex justify-between">
                            <span class="text-gray-600">Product</span>
                            <span class="font-medium text-gray-900">Custom Fabric Order</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Fabric Type</span>
                            <span class="font-medium text-gray-900">{{ $order->fabric_type ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Quantity</span>
                            <span class="font-medium text-gray-900">{{ $order->formatted_fabric_quantity ?? ($order->fabric_quantity_meters . ' m') }}</span>
                        </div>
                        @if(!empty($order->intended_use_label))
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Intended Use</span>
                                <span class="font-medium text-gray-900">{{ $order->intended_use_label }}</span>
                            </div>
                        @endif
                    @else
                        <div class="flex justify-between">
                            <span class="text-gray-600">Product</span>
                            <span class="font-medium text-gray-900">{{ $order->product->name ?? 'Custom Product' }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Quantity</span>
                            <span class="font-medium text-gray-900">{{ $order->quantity }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between pt-2 mt-1 border-t border-dashed border-gray-200">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-red-600">₱{{ number_format($order->final_price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Selection -->
        <form method="POST" action="{{ route('custom_orders.payment.process', $order->id) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Payment Method</h2>

            <div class="space-y-3">
                <!-- GCash Payment -->
                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:border-blue-500 {{ old('payment_method') === 'online_banking' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="payment_method" value="online_banking" {{ old('payment_method') === 'online_banking' ? 'checked' : '' }} class="mr-4">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900">GCash</div>
                        <p class="text-sm text-gray-600">Pay using GCash e-wallet</p>
                    </div>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </label>

                <!-- Bank Transfer -->
                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:border-blue-500 {{ old('payment_method') === 'bank_transfer' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }} class="mr-4">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900">Bank Transfer</div>
                        <p class="text-sm text-gray-600">Direct transfer to our bank account</p>
                    </div>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                </label>
            </div>

            @error('payment_method')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full mt-6 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition-colors duration-200">
                Continue to Payment
            </button>
        </form>

        <!-- Back Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('custom_orders.show', $order) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                ← Back to Order Details
            </a>
        </div>

    </div>
</div>
@endsection
