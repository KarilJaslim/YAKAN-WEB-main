<!-- Notification Dropdown -->
<div class="relative" x-data="notificationDropdown()" @click.outside="open = false">
    
    <!-- Notification Button -->
    <button @click="open = !open; loadNotifications()" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
        <i class="fas fa-bell text-xl"></i>
        <span id="notification-badge" 
              x-show="unreadCount > 0" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
              x-text="unreadCount">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500" x-show="unreadCount > 0">
                    <span x-text="unreadCount"></span> unread
                </span>
                <button x-show="unreadCount > 0" 
                        @click="markAllAsRead()" 
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Mark all read
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-for="notification in notifications" :key="notification.id">
                <div :class="{'bg-blue-50': !notification.is_read}" class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer">
                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div :class="`w-8 h-8 bg-${notification.color || 'blue'}-100 rounded-full flex items-center justify-center`">
                                <i :class="notification.icon || 'fas fa-bell'" :class="`text-${notification.color || 'blue'}-600 text-sm`"></i>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-medium text-gray-900 truncate" x-text="notification.title"></h4>
                                <button @click.stop="markAsRead(notification.id)" class="text-xs text-blue-600 hover:text-blue-800" x-show="!notification.is_read">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2" x-text="notification.message"></p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-gray-500" x-text="notification.created_at"></span>
                                <a :href="notification.url" x-show="notification.url" class="text-xs text-blue-600 hover:text-blue-800 font-medium" @click="markAsRead(notification.id)">
                                    View →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="notifications.length === 0" class="px-4 py-8 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-bell-slash text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600">No new notifications</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            <a href="/notifications" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                View All Notifications →
            </a>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            // Load unread count on page load
            this.loadUnreadCount();
            
            // Poll for new notifications every 30 seconds
            setInterval(() => {
                this.loadUnreadCount();
            }, 30000);
        },
        
        async loadNotifications() {
            try {
                const response = await fetch('/api/v1/notifications', {
                    headers: {
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
            }
        },
        
        async loadUnreadCount() {
            try {
                const response = await fetch('/api/v1/notifications/unread-count', {
                    headers: {
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.unreadCount = data.unread_count || 0;
                }
            } catch (error) {
                console.error('Failed to load unread count:', error);
            }
        },
        
        async markAsRead(id) {
            try {
                const response = await fetch(`/api/v1/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    // Update the notification in the list
                    const notification = this.notifications.find(n => n.id === id);
                    if (notification) {
                        notification.is_read = true;
                    }
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                const response = await fetch('/api/v1/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.getAuthToken()}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    // Mark all notifications as read in the list
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Failed to mark all notifications as read:', error);
            }
        },
        
        getAuthToken() {
            // Get token from localStorage, cookie, or meta tag
            return localStorage.getItem('auth_token') || 
                   document.querySelector('meta[name="api-token"]')?.content || 
                   '';
        }
    }
}
</script>
