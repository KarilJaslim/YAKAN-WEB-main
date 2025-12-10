@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.custom_orders.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Orders
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }} - Details</h1>
            <p class="text-gray-600 mt-1">Created {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Status Badge --}}
            @php
                $displayStatusLabel = $order->status === 'completed' ? 'Delivered' : ucfirst(str_replace('_', ' ', $order->status));
            @endphp
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                {{ $order->status === 'delivered' || $order->status === 'completed' ? 'bg-green-100 text-green-700' : 
                   ($order->status === 'out_for_delivery' ? 'bg-blue-100 text-blue-700' : 
                   ($order->status === 'production_complete' ? 'bg-purple-100 text-purple-700' : 
                   ($order->status === 'processing' || $order->status === 'in_production' ? 'bg-orange-100 text-orange-700' : 
                   ($order->status === 'cancelled' || $order->status === 'rejected' ? 'bg-red-100 text-red-700' : 
                   'bg-yellow-100 text-yellow-700')))) }}">
                {{ $displayStatusLabel }}
            </span>
            
            {{-- Payment Status Badge --}}
            @php
                $paymentClass = match($order->payment_status) {
                    'paid' => 'bg-green-100 text-green-700',
                    'pending' => 'bg-orange-100 text-orange-700',
                    'failed' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-700',
                };
                $paymentLabel = match($order->payment_status) {
                    'paid' => 'Paid',
                    'pending' => 'Pending',
                    'failed' => 'Failed',
                    default => 'Unpaid',
                };
            @endphp
            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $paymentClass }}">
                {{ $paymentLabel }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content - Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Pattern Preview Section --}}
            @if($order->design_upload)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Pattern Preview
                    @if($order->design_method === 'pattern')
                        <span class="text-sm font-normal text-purple-600">(Customized Pattern)</span>
                    @endif
                </h2>
                
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border-2 border-purple-200">
                    @if(str_starts_with($order->design_upload, 'data:image'))
                        <img src="{{ $order->design_upload }}" alt="Pattern Preview" 
                             class="w-full max-h-96 object-contain rounded-lg shadow-lg">
                    @else
                        <img src="{{ asset('storage/' . $order->design_upload) }}" alt="Pattern Preview" 
                             class="w-full max-h-96 object-contain rounded-lg shadow-lg">
                    @endif
                </div>
                
                {{-- Customization Settings --}}
                @if($order->design_metadata && is_array($order->design_metadata))
                    @if(isset($order->design_metadata['customization_settings']))
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                            <h3 class="col-span-full text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                Customization Settings
                            </h3>
                            @foreach($order->design_metadata['customization_settings'] as $key => $value)
                                <div class="bg-white rounded-lg p-3 border-2 border-purple-200 hover:border-purple-400 transition-colors">
                                    <div class="text-xs text-gray-500 uppercase font-semibold">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                    <div class="text-sm font-bold text-purple-900">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
            @endif

            {{-- Order Details --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Fabric Type --}}
                    @if($order->fabric_type)
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="text-sm text-purple-600 font-semibold mb-1">Fabric Type</div>
                        <div class="text-lg font-bold text-gray-900">{{ ucfirst($order->fabric_type) }}</div>
                    </div>
                    @endif
                    
                    {{-- Quantity --}}
                    @if($order->fabric_quantity_meters)
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="text-sm text-blue-600 font-semibold mb-1">Quantity</div>
                        <div class="text-lg font-bold text-gray-900">{{ $order->fabric_quantity_meters }} meters</div>
                    </div>
                    @endif
                    
                    {{-- Intended Use --}}
                    @if($order->intended_use)
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="text-sm text-green-600 font-semibold mb-1">Intended Use</div>
                        <div class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->intended_use)) }}</div>
                    </div>
                    @endif
                    
                    {{-- Design Method --}}
                    @if($order->design_method)
                    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                        <div class="text-sm text-indigo-600 font-semibold mb-1">Design Method</div>
                        <div class="text-lg font-bold text-gray-900">{{ ucfirst($order->design_method) }}</div>
                    </div>
                    @endif
                </div>
                
                {{-- Specifications --}}
                @if($order->specifications)
                <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="text-sm text-gray-600 font-semibold mb-2">Specifications:</div>
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $order->specifications }}</p>
                </div>
                @endif
                
                {{-- Special Requirements --}}
                @if($order->special_requirements)
                <div class="mt-4 bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                    <div class="text-sm text-yellow-800 font-semibold mb-2">Special Requirements:</div>
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $order->special_requirements }}</p>
                </div>
                @endif
            </div>

            {{-- Pricing Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Pricing</h2>
                
                <div class="space-y-3">
                    @if($order->estimated_price)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">Estimated Price:</span>
                        <span class="text-lg font-semibold text-gray-900">₱{{ number_format($order->estimated_price, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->final_price)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">Final Price:</span>
                        <span class="text-2xl font-bold text-green-600">₱{{ number_format($order->final_price, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->payment_method)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $order->payment_method === 'online_banking' ? 'GCash' :
                               ($order->payment_method === 'gcash' ? 'GCash' :
                               ($order->payment_method === 'bank_transfer' ? 'Bank Transfer' : ucfirst(str_replace('_', ' ', $order->payment_method)))) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar - Right Column --}}
        <div class="space-y-6">
            
            {{-- Customer Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Customer</h2>
                
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-lg font-bold text-white">{{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">{{ $order->user->email ?? 'N/A' }}</div>
                    </div>
                </div>
                
                @if($order->phone)
                <div class="text-sm text-gray-600 mb-2">
                    <span class="font-semibold">Phone:</span> {{ $order->phone }}
                </div>
                @endif

                <div class="text-sm text-gray-600 mb-2">
                    <span class="font-semibold">Delivery:</span>
                    <span class="ml-1 font-medium text-gray-800">
                        @if($order->delivery_type === 'pickup')
                            Store Pickup
                        @elseif($order->delivery_type === 'delivery')
                            Delivery
                        @else
                            Not specified
                        @endif
                    </span>
                </div>
                
                @if($order->delivery_address)
                <div class="text-sm text-gray-600">
                    <span class="font-semibold">Address:</span>
                    <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $order->delivery_address }}</p>
                </div>
                @endif
            </div>

            {{-- Payment Information --}}
            @if($order->payment_method || $order->payment_status !== 'unpaid')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Information</h2>
                
                {{-- Payment Status --}}
                <div class="mb-4">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Payment Status</div>
                    @php
                        $paymentStatusClass = match($order->payment_status) {
                            'paid' => 'bg-green-100 text-green-700',
                            'pending' => 'bg-orange-100 text-orange-700',
                            'failed' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700',
                        };

                        $paymentStatusLabel = match($order->payment_status) {
                            'paid' => '✓ Paid',
                            'pending' => '⏳ Pending Verification',
                            'failed' => '✗ Failed',
                            default => 'Unpaid',
                        };
                    @endphp
                    <span class="px-3 py-1.5 rounded-full text-sm font-semibold inline-block {{ $paymentStatusClass }}">
                        {{ $paymentStatusLabel }}
                    </span>
                </div>

                {{-- Payment Method --}}
                @if($order->payment_method)
                <div class="mb-4">
                    <div class="text-sm font-semibold text-gray-700 mb-1">Payment Method</div>
                    <div class="text-sm text-gray-900">
                        {{ $order->payment_method === 'online_banking' ? 'GCash' :
                           ($order->payment_method === 'gcash' ? 'GCash' :
                           ($order->payment_method === 'bank_transfer' ? 'Bank Transfer' : ucfirst(str_replace('_', ' ', $order->payment_method)))) }}
                    </div>
                </div>
                @endif

                {{-- Transaction ID --}}
                @if($order->transaction_id)
                <div class="mb-4">
                    <div class="text-sm font-semibold text-gray-700 mb-1">Transaction ID</div>
                    <div class="text-xs font-mono text-gray-900 bg-gray-50 px-2 py-1 rounded">{{ $order->transaction_id }}</div>
                </div>
                @endif

                {{-- Payment Receipt --}}
                @if($order->payment_receipt)
                <div class="mb-4">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Payment Receipt</div>
                    @php
                        $receiptUrl = str_starts_with($order->payment_receipt, 'payment_receipts/') 
                            ? asset('uploads/' . $order->payment_receipt)
                            : asset('storage/' . $order->payment_receipt);
                    @endphp
                    <a href="{{ $receiptUrl }}" target="_blank" 
                       class="block bg-gray-50 border border-gray-200 rounded-lg p-2 hover:bg-gray-100 transition">
                        <img src="{{ $receiptUrl }}" alt="Payment Receipt" 
                             class="w-full h-32 object-contain rounded">
                        <div class="text-xs text-center text-blue-600 mt-1">Click to view full size</div>
                    </a>
                </div>
                @endif

                {{-- Amount Paid --}}
                @if($order->final_price && $order->payment_status !== 'unpaid')
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Amount {{ $order->payment_status === 'paid' ? 'Paid' : 'Submitted' }}</span>
                        <span class="text-lg font-bold text-green-600">₱{{ number_format($order->final_price, 2) }}</span>
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Admin Actions --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Quick Actions
                </h2>
                
                <div class="space-y-4" id="adminActions">
                    {{-- Update Status --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Order Status
                        </label>
                        <form id="statusForm" data-order-id="{{ $order->id }}">
                            @csrf
                            <select name="status" id="statusSelect" class="w-full border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg px-4 py-2.5 mb-3 transition-all font-medium text-gray-700">
                                @php
                                    $statusHierarchy = ['pending' => 0, 'price_quoted' => 1, 'approved' => 2, 'in_production' => 3, 'production_complete' => 4, 'out_for_delivery' => 5, 'delivered' => 6, 'completed' => 6, 'cancelled' => 7];
                                    $currentLevel = $statusHierarchy[$order->status] ?? 0;
                                @endphp
                                <option value="pending" @selected($order->status === 'pending') @disabled($currentLevel >= 1)>⏳ Pending Review</option>
                                <option value="price_quoted" @selected($order->status === 'price_quoted') @disabled($currentLevel >= 2)>💰 Price Quoted</option>
                                <option value="approved" @selected($order->status === 'approved') @disabled($currentLevel >= 3)>✅ Approved</option>
                                <option value="in_production" @selected($order->status === 'in_production') @disabled($currentLevel >= 4)>🔨 In Production</option>
                                <option value="production_complete" @selected($order->status === 'production_complete') @disabled($currentLevel >= 5)>✓ Production Complete</option>
                                <option value="out_for_delivery" @selected($order->status === 'out_for_delivery') @disabled($currentLevel >= 6)>🚚 Out for Delivery</option>
                                <option value="delivered" @selected($order->status === 'delivered' || $order->status === 'completed')>🎉 Delivered</option>
                                <option value="cancelled" @selected($order->status === 'cancelled')>❌ Cancelled</option>
                            </select>
                            <button type="submit" id="statusBtn" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Update Status</span>
                            </button>
                        </form>
                    </div>
                    
                    {{-- Set Price --}}
                    @if($order->status === 'pending' || !$order->final_price)
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Quote Final Price
                        </label>
                        <form id="priceForm" data-order-id="{{ $order->id }}">
                            @csrf
                            <div class="relative mb-2">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">₱</span>
                                <input type="number" name="price" step="0.01" min="0" 
                                       value="{{ $order->final_price ?? $order->estimated_price }}"
                                       class="w-full border-2 border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg pl-8 pr-4 py-2.5 transition-all font-medium" 
                                       placeholder="0.00" required>
                            </div>
                            <textarea name="notes" rows="2" 
                                      class="w-full border-2 border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg px-4 py-2.5 mb-3 transition-all text-sm" 
                                      placeholder="Add pricing notes or details (optional)"></textarea>
                            <button type="submit" id="priceBtn" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Send Quote to Customer</span>
                            </button>
                        </form>
                    </div>
                    @endif
                    
                    {{-- Verify Payment --}}
                    @if(in_array($order->payment_status, ['pending', 'pending_verification']))
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-lg p-4 border border-yellow-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Payment Verification
                        </label>
                        <form id="paymentForm" data-order-id="{{ $order->id }}">
                            @csrf
                            <select name="payment_status" class="w-full border-2 border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 rounded-lg px-4 py-2.5 mb-3 transition-all font-medium">
                                <option value="paid">✅ Confirm Payment Received</option>
                                <option value="failed">❌ Mark Payment as Failed</option>
                            </select>
                            <button type="submit" id="paymentBtn" class="w-full bg-gradient-to-r from-yellow-600 to-amber-600 hover:from-yellow-700 hover:to-amber-700 text-white font-semibold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Verify Payment</span>
                            </button>
                        </form>
                    </div>
                    @endif
                    
                    {{-- Reject Order --}}
                    @if($order->status === 'pending')
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-lg p-4 border border-red-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Reject Order
                        </label>
                        <form id="rejectForm" data-order-id="{{ $order->id }}">
                            @csrf
                            <textarea name="rejection_reason" rows="2" 
                                      class="w-full border-2 border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 rounded-lg px-4 py-2.5 mb-3 transition-all text-sm" 
                                      placeholder="Explain why this order is being rejected (required)" required></textarea>
                            <button type="submit" id="rejectBtn" class="w-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold py-3 px-4 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Reject Order</span>
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Success/Error Messages --}}
                    <div id="actionMessage" class="hidden rounded-lg p-4 transition-all"></div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Order Timeline
                </h2>
                
                <div class="relative">
                    {{-- Timeline line --}}
                    <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-200 via-green-200 to-gray-200"></div>
                    
                    <div class="space-y-6 relative">
                        {{-- Order Created --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-blue-50 rounded-lg p-3 border border-blue-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-blue-900">📝 Order Created</span>
                                </div>
                                <div class="text-sm text-blue-700">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-blue-600 mt-1">{{ $order->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        @if($order->price_quoted_at)
                        {{-- Price Quoted --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-green-50 rounded-lg p-3 border border-green-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-green-900">💰 Price Quoted</span>
                                    @if($order->final_price)
                                    <span class="text-xs bg-green-600 text-white px-2 py-0.5 rounded-full">₱{{ number_format($order->final_price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="text-sm text-green-700">{{ \Carbon\Carbon::parse($order->price_quoted_at)->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-green-600 mt-1">{{ \Carbon\Carbon::parse($order->price_quoted_at)->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->approved_at)
                        {{-- Approved --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-emerald-50 rounded-lg p-3 border border-emerald-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-emerald-900">✅ Order Approved</span>
                                </div>
                                <div class="text-sm text-emerald-700">{{ \Carbon\Carbon::parse($order->approved_at)->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-emerald-600 mt-1">{{ \Carbon\Carbon::parse($order->approved_at)->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->rejected_at)
                        {{-- Rejected --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-red-50 rounded-lg p-3 border border-red-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-red-900">❌ Order Rejected</span>
                                </div>
                                <div class="text-sm text-red-700">{{ \Carbon\Carbon::parse($order->rejected_at)->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-red-600 mt-1">{{ \Carbon\Carbon::parse($order->rejected_at)->diffForHumans() }}</div>
                                @if($order->rejection_reason)
                                <div class="mt-2 p-2 bg-red-100 rounded text-xs text-red-800 border-l-4 border-red-500">
                                    <strong>Reason:</strong> {{ $order->rejection_reason }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status === 'in_production' || $order->production_completed_at)
                        {{-- In Production --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 {{ $order->production_completed_at ? 'bg-orange-500' : 'bg-orange-500 animate-pulse' }} rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-orange-50 rounded-lg p-3 border border-orange-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-orange-900">🔨 {{ $order->production_completed_at ? 'Production Finished' : 'Currently in Production' }}</span>
                                    @if(!$order->production_completed_at && $order->status === 'in_production')
                                    <span class="flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-orange-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                                    </span>
                                    @endif
                                </div>
                                @if($order->production_completed_at)
                                <div class="text-sm text-orange-700">{{ \Carbon\Carbon::parse($order->production_completed_at)->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-orange-600 mt-1">{{ \Carbon\Carbon::parse($order->production_completed_at)->diffForHumans() }}</div>
                                @else
                                <div class="text-xs text-orange-700">Active</div>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($order->out_for_delivery_at)
                        {{-- Out for Delivery --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-blue-50 rounded-lg p-3 border border-blue-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-blue-900">🚚 Out for Delivery</span>
                                </div>
                                <div class="text-sm text-blue-700">{{ \Carbon\Carbon::parse($order->out_for_delivery_at)->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-blue-600 mt-1">{{ \Carbon\Carbon::parse($order->out_for_delivery_at)->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status === 'delivered' || $order->status === 'completed')
                        {{-- Delivered --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-green-50 rounded-lg p-3 border border-green-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-green-900">🎉 Successfully Delivered</span>
                                </div>
                                @if($order->delivered_at)
                                <div class="text-sm text-green-700">{{ \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-green-600 mt-1">{{ \Carbon\Carbon::parse($order->delivered_at)->diffForHumans() }}</div>
                                @else
                                <div class="text-xs text-green-700">Completed</div>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($order->status === 'completed' && $order->status !== 'delivered')
                        {{-- Completed --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-purple-50 rounded-lg p-3 border border-purple-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-purple-900">🎉 Order Completed</span>
                                </div>
                                <div class="text-xs text-purple-700">Successfully delivered</div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Last Updated --}}
                        <div class="flex items-start gap-4">
                            <div class="relative z-10">
                                <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center shadow">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-700">Last Updated</span>
                                </div>
                                <div class="text-sm text-gray-600">{{ $order->updated_at->format('M d, Y h:i A') }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $order->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Automated AJAX Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const messageDiv = document.getElementById('actionMessage');
    
    // Show message helper
    function showMessage(message, type = 'success') {
        messageDiv.className = `rounded-lg p-4 transition-all mb-4 ${
            type === 'success' 
                ? 'bg-green-100 border-2 border-green-500 text-green-800' 
                : 'bg-red-100 border-2 border-red-500 text-red-800'
        }`;
        messageDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    }
                </svg>
                <span class="font-semibold">${message}</span>
            </div>
        `;
        messageDiv.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 5000);
        
        // Scroll to message
        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Button loading state
    function setButtonLoading(button, loading) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = `
                <svg class="animate-spin h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2">Processing...</span>
            `;
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalHtml;
        }
    }
    
    // Update Status Form
    const statusForm = document.getElementById('statusForm');
    if (statusForm) {
        statusForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = document.getElementById('statusBtn');
            const orderId = this.dataset.orderId;
            const formData = new FormData(this);
            
            setButtonLoading(button, true);
            
            try {
                const response = await fetch(`/admin/custom-orders/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: formData.get('status')
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('✅ Order status updated successfully! Customer has been notified.', 'success');
                    
                    // Reload page after 2 seconds to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage('❌ ' + (data.message || 'Failed to update status'), 'error');
                    setButtonLoading(button, false);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('❌ Network error. Please check your connection and try again.', 'error');
                setButtonLoading(button, false);
            }
        });
    }
    
    // Quote Price Form
    const priceForm = document.getElementById('priceForm');
    if (priceForm) {
        priceForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = document.getElementById('priceBtn');
            const orderId = this.dataset.orderId;
            const formData = new FormData(this);
            
            setButtonLoading(button, true);
            
            try {
                const response = await fetch(`/admin/custom-orders/${orderId}/quote-price`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        price: formData.get('price'),
                        notes: formData.get('notes')
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('✅ Price quote sent successfully! Customer will be notified via email.', 'success');
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage('❌ ' + (data.message || 'Failed to send price quote'), 'error');
                    setButtonLoading(button, false);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('❌ Network error. Please try again.', 'error');
                setButtonLoading(button, false);
            }
        });
    }
    
    // Payment Verification Form
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = document.getElementById('paymentBtn');
            const orderId = this.dataset.orderId;
            const formData = new FormData(this);
            
            setButtonLoading(button, true);
            
            try {
                const response = await fetch(`/admin/custom-orders/${orderId}/verify-payment`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_status: formData.get('payment_status')
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const status = formData.get('payment_status');
                    const message = status === 'paid' 
                        ? '✅ Payment verified successfully! Order can now proceed to production.'
                        : '⚠️ Payment marked as failed. Customer will be notified.';
                    showMessage(message, 'success');
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage('❌ ' + (data.message || 'Failed to update payment status'), 'error');
                    setButtonLoading(button, false);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('❌ Network error. Please try again.', 'error');
                setButtonLoading(button, false);
            }
        });
    }
    
    // Reject Order Form
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Confirmation dialog
            const confirmed = confirm('⚠️ Are you sure you want to reject this order? This action will notify the customer.');
            if (!confirmed) return;
            
            const button = document.getElementById('rejectBtn');
            const orderId = this.dataset.orderId;
            const formData = new FormData(this);
            
            setButtonLoading(button, true);
            
            try {
                const response = await fetch(`/admin/custom-orders/${orderId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        rejection_reason: formData.get('rejection_reason')
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('✅ Order rejected. Customer has been notified with the reason provided.', 'success');
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage('❌ ' + (data.message || 'Failed to reject order'), 'error');
                    setButtonLoading(button, false);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('❌ Network error. Please try again.', 'error');
                setButtonLoading(button, false);
            }
        });
    }
    
    // Auto-dismiss Laravel flash messages
    const flashMessages = document.querySelectorAll('.alert');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }, 5000);
    });
});
</script>
@endsection
