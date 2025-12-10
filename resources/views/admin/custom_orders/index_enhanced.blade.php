@extends('layouts.admin')

@section('title', 'Custom Orders Management - Enhanced')

@push('styles')
<style>
    /* Enhanced Animations */
    @keyframes slideInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Enhanced Status Badges */
    .status-badge {
        @apply px-3 py-1 rounded-full text-xs font-medium relative overflow-hidden;
        transition: all 0.3s ease;
    }
    
    .status-pending { 
        @apply bg-yellow-100 text-yellow-800 border border-yellow-200; 
    }
    .status-processing { 
        @apply bg-blue-100 text-blue-800 border border-blue-200;
    }
    .status-completed { 
        @apply bg-green-100 text-green-800 border border-green-200;
    }
    .status-cancelled { 
        @apply bg-red-100 text-red-800 border border-red-200;
    }
    
    /* Statistics Cards */
    .stat-card {
        @apply bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow;
        animation: slideInUp 0.5s ease-out;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    /* Table Enhancements */
    .enhanced-table {
        @apply bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden;
    }
    
    .enhanced-table th {
        @apply bg-gray-50 text-gray-700 font-semibold text-sm border-b border-gray-200;
    }
    
    .enhanced-table td {
        @apply border-b border-gray-100;
    }
    
    .enhanced-table tr:hover {
        @apply bg-gray-50;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto py-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Custom Orders</h1>
            <p class="text-gray-600 mt-1">Manage and track custom order requests</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.custom_orders.production-dashboard') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Production Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    @isset($totalOrders)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Today's Orders</p>
                    <p class="text-2xl font-bold text-green-600">{{ $todayOrders }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-purple-600">₱{{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('custom_orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, order ID..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="md:col-span-4 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Filter Orders
                </button>
                <a href="{{ route('custom_orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="enhanced-table">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Custom Orders List</h2>
        </div>
        
        @if($orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Preview</th>
                        <th class="px-6 py-3 text-left">Fabric Details</th>
                        <th class="px-6 py-3 text-left">Delivery</th>
                        <th class="px-6 py-3 text-left">Price</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Payment</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">#{{ $order->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $order->user ? $order->user->name : 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $order->user ? $order->user->email : $order->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->design_upload)
                                <div class="relative group">
                                    @if(str_starts_with($order->design_upload, 'data:image'))
                                        {{-- Base64 image --}}
                                        <img src="{{ $order->design_upload }}" alt="Pattern Preview" 
                                             class="w-16 h-16 rounded-lg object-cover border-2 border-purple-200 shadow-sm">
                                    @else
                                        {{-- File path --}}
                                        <img src="{{ asset('storage/' . $order->design_upload) }}" alt="Pattern Preview" 
                                             class="w-16 h-16 rounded-lg object-cover border-2 border-purple-200 shadow-sm">
                                    @endif
                                    @if($order->design_method === 'pattern')
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-purple-600 text-white text-xs rounded-full flex items-center justify-center" title="Pattern Customization">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                            </svg>
                                        </span>
                                    @endif
                                    {{-- Hover preview --}}
                                    <div class="absolute left-0 top-0 w-64 h-64 bg-white rounded-lg shadow-2xl border-2 border-purple-300 p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 -translate-y-2 pointer-events-none">
                                        @if(str_starts_with($order->design_upload, 'data:image'))
                                            <img src="{{ $order->design_upload }}" alt="Pattern Preview" class="w-full h-full object-contain rounded">
                                        @else
                                            <img src="{{ asset('storage/' . $order->design_upload) }}" alt="Pattern Preview" class="w-full h-full object-contain rounded">
                                        @endif
                                        <p class="text-xs text-center text-gray-600 mt-1">Pattern Preview</p>
                                    </div>
                                </div>
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-dashed border-gray-300">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                @if($order->fabric_type)
                                    <p class="font-medium text-gray-900">{{ ucfirst($order->fabric_type) }}</p>
                                    <p class="text-gray-500">{{ $order->formatted_fabric_quantity ?? $order->fabric_quantity_meters . ' m' }}</p>
                                @else
                                    <p class="text-gray-500">N/A</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600 max-w-xs">
                                <div class="font-semibold text-gray-800">
                                    @if($order->delivery_type === 'pickup')
                                        Store Pickup
                                    @elseif($order->delivery_type === 'delivery')
                                        Delivery
                                    @else
                                        Not specified
                                    @endif
                                </div>
                                @if($order->delivery_address)
                                    <div class="mt-1 text-[11px] text-gray-500 truncate" title="{{ $order->delivery_address }}">
                                        {{ $order->delivery_address }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">₱{{ number_format($order->final_price ?? $order->estimated_price ?? 0, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge status-{{ $order->status }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @php
                                    $paymentStatusClass = match($order->payment_status) {
                                        'paid' => 'status-completed',
                                        'pending', 'pending_verification' => 'status-pending',
                                        'failed' => 'status-cancelled',
                                        default => 'status-pending',
                                    };
                                @endphp
                                <span class="status-badge {{ $paymentStatusClass }}">
                                    {{ $order->payment_status ? ucfirst(str_replace('_', ' ', $order->payment_status)) : 'Unpaid' }}
                                </span>
                                @if($order->payment_method)
                                    <p class="text-xs text-gray-500">
                                        {{ $order->payment_method === 'online_banking' ? 'GCash' : 
                                           ($order->payment_method === 'gcash' ? 'GCash' : 
                                           ($order->payment_method === 'bank_transfer' ? 'Bank Transfer' : ucfirst(str_replace('_', ' ', $order->payment_method)))) }}
                                    </p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $order->created_at->format('M d, Y') }}
                                <p class="text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.custom_orders.show', $order->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                    View
                                </a>
                                @if($order->status === 'pending')
                                    <form action="{{ route('admin.custom_orders.approve', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 font-medium text-sm"
                                                onclick="return confirm('Approve this order?')">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.custom_orders.reject', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm"
                                                onclick="return confirm('Reject this order?')">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No custom orders found</h3>
            <p class="text-gray-500">Get started by creating custom orders or adjust your filters.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateOrderStatus(orderId, newStatus) {
    if (confirm('Are you sure you want to update this order status?')) {
        fetch(`/admin/custom-orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating order status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    }
}
</script>
@endpush
