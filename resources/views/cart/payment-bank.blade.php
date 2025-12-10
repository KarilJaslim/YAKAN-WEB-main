@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                <span class="text-green-600">🏦</span>
                Bank Transfer Payment
            </h1>
            <p class="text-gray-600">Transfer payment to complete your order</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Bank Transfer Instructions -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Instructions Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">
                        Bank Transfer Instructions
                    </h2>

                    <div class="space-y-6">
                        <!-- Step 1 -->
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold">
                                1
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Transfer to our bank account</h3>
                                <p class="text-gray-600 text-sm">Use the bank details provided below to make your payment.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold">
                                2
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Include your Order ID in reference</h3>
                                <p class="text-gray-600 text-sm">Make sure to include Order ID: <span class="font-bold text-gray-900">#{{ $order->id }}</span> in your transfer reference.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold">
                                3
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Upload proof of payment</h3>
                                <p class="text-gray-600 text-sm">Upload your payment receipt or screenshot below. Your payment will be verified automatically and your order will start processing immediately!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details Card -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl shadow-lg p-6 border-2 border-green-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                        Bank Account Details
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-sm text-gray-600 mb-1">Bank Name</div>
                            <div class="font-bold text-gray-900">BDO Unibank</div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-sm text-gray-600 mb-1">Account Name</div>
                            <div class="font-bold text-gray-900">Your Company Name</div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-sm text-gray-600 mb-1">Account Number</div>
                            <div class="font-bold text-gray-900 text-xl">1234-5678-9012</div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="text-sm text-gray-600 mb-1">Amount to Transfer</div>
                            <div class="font-bold text-green-600 text-2xl">₱{{ number_format($order->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Upload Proof -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Upload Proof of Payment
                    </h2>
                    
                    <form action="{{ route('payment.bank', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Receipt</label>
                            <input type="file" name="receipt" accept="image/*" required
                                   class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <p class="mt-2 text-xs text-gray-500">Upload a clear image of your payment receipt or bank transfer confirmation.</p>
                        </div>

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4 rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl font-semibold">
                            Submit Proof of Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">
                        Order Summary
                    </h2>

                    <!-- Order Items -->
                    <div class="space-y-3 mb-6">
                        @foreach($order->orderItems as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                <span class="font-medium text-gray-900">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-green-600">₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Order Info -->
                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Order ID:</span>
                            <span class="font-medium text-gray-900">#{{ $order->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Payment Method:</span>
                            <span class="font-medium text-gray-900">Bank Transfer</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-lg text-xs font-medium">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-blue-800">
                                <span class="font-semibold">Note:</span> Your order will be processed once we verify your payment. This usually takes 1-2 business days.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection