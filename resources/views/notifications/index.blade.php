@extends('layouts.app')

@section('title', 'Notifications - Yakan E-Commerce')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-gray-600 mt-1">Stay updated with your orders and account activity</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm">
                        <span class="text-gray-500">Unread:</span>
                        <span class="font-semibold text-blue-600">{{ $unreadCount }}</span>
                    </div>
                    @if($unreadCount > 0)
                        <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Mark All as Read
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Filter:</label>
                    <select id="filterSelect" onchange="applyFilters()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Notifications</option>
                        <option value="unread" {{ request('filter') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('filter') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Type:</label>
                    <select id="typeSelect" onchange="applyFilters()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>Orders</option>
                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payments</option>
                        <option value="shipping" {{ request('type') == 'shipping' ? 'selected' : '' }}>Shipping</option>
                        <option value="custom_order" {{ request('type') == 'custom_order' ? 'selected' : '' }}>Custom Orders</option>
                        <option value="promotion" {{ request('type') == 'promotion' ? 'selected' : '' }}>Promotions</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow notification-item {{ !$notification->is_read ? 'border-l-4 border-l-blue-500' : '' }}" data-notification-id="{{ $notification->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 flex-1">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-{{ $notification->color ?? 'gray' }}-100 rounded-full flex items-center justify-center">
                                    <i class="{{ $notification->icon ?? 'fas fa-bell' }} text-{{ $notification->color ?? 'gray' }}-600 text-lg"></i>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="text-lg font-semibold text-gray-900 {{ !$notification->is_read ? 'font-bold' : '' }}">
                                        {{ $notification->title }}
                                    </h3>
                                    @if(!$notification->is_read)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-600 mb-2">{{ $notification->message }}</p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if($notification->url)
                                        <a href="{{ $notification->url }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            View Details →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2 ml-4">
                            @if(!$notification->is_read)
                                <button onclick="markAsRead({{ $notification->id }})" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Mark as read">
                                    <i class="fas fa-check"></i>
                                </button>
                            @else
                                <button onclick="markAsUnread({{ $notification->id }})" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Mark as unread">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            @endif
                            <button onclick="deleteNotification({{ $notification->id }})" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-600">We'll notify you when there are updates to your orders or account.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function applyFilters() {
    const filter = document.getElementById('filterSelect').value;
    const type = document.getElementById('typeSelect').value;
    
    const url = new URL(window.location);
    if (filter) url.searchParams.set('filter', filter);
    else url.searchParams.delete('filter');
    
    if (type) url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    
    window.location.href = url.toString();
}

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAsUnread(notificationId) {
    fetch(`/notifications/${notificationId}/unread`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    
    fetch('/notifications/mark-all-read', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(notificationId) {
    if (!confirm('Delete this notification?')) return;
    
    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
