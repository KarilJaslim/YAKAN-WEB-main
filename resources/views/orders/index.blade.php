@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-4">My Orders</h1>
            <p class="text-xl text-gray-600">View and track your recent orders and purchases.</p>
        </div>

        @if($orders->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-12 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No orders yet</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Start shopping and place your first order to see it appear here.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold rounded-xl hover:from-red-700 hover:to-red-800 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Start Shopping
                </a>
            </div>
        @else
            {{-- Orders Table --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Order History</h2>
                            <p class="text-gray-600 mt-1">{{ $orders->count() }} {{ $orders->count() === 1 ? 'order' : 'orders' }} placed</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-600">Active</span>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Order #</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Tracking #</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Date</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Items</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Total</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-4 text-right text-sm font-bold text-gray-900 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">#{{ $order->id }}</div>
                                                <div class="text-sm text-gray-500">Order ID</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3" style="background-color: rgba(128, 0, 0, 0.1);">
                                                <svg class="w-5 h-5" style="color: #800000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-mono font-bold text-gray-900">{{ $order->tracking_number }}</div>
                                                <a href="{{ route('track-order.show', $order->tracking_number) }}" 
                                                   class="text-xs hover:underline" style="color: #800000;">
                                                    Track Order →
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-gray-900">{{ $order->orderItems->sum('quantity') }}</span>
                                            <span class="text-sm text-gray-500 ml-1">items</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-xl text-red-600">₱{{ number_format($order->total_amount, 2) }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $statusColor = match($order->status ?? 'pending') {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'shipped' => 'bg-purple-100 text-purple-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-3 py-1 {{ $statusColor }} rounded-full text-xs font-bold">
                                                {{ ucfirst($order->status ?? 'pending') }}
                                            </span>
                                            @if($order->tracking_status === 'Out for Delivery')
                                                <span class="inline-flex items-center px-2 py-0.5 bg-orange-100 text-orange-800 rounded-full text-[11px] font-semibold">
                                                    🚛 Out for Delivery
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Continue Shopping --}}
        <div class="mt-12 flex items-center justify-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
