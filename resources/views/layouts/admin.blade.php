<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Yakan E-commerce Admin</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
        
    <!-- Simple CSS instead of Vite -->
    <style>
        /* Enhanced scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.5);
        }
        
        /* Smooth transitions */
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Active state indicator */
        .nav-link-active {
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-left: 4px solid #3b82f6;
            color: #3b82f6;
        }
        
                
        /* Animated gradients */
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        /* Fade in animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Progress bar animations */
        @keyframes progressAnimate {
            from { width: 0%; }
            to { width: var(--progress-width); }
        }
        
        .progress-animate {
            animation: progressAnimate 1s ease-out forwards;
        }
        
        /* Basic layout styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 20px;
        }
        
        .grid {
            display: grid;
            gap: 20px;
        }
        
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-cols-5 { grid-template-columns: repeat(5, 1fr); }
        
        @media (max-width: 768px) {
            .grid-cols-4, .grid-cols-5 { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 480px) {
            .grid-cols-4, .grid-cols-5 { grid-template-columns: 1fr; }
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Responsive Sidebar Styles */
        .sidebar-overlay {
            @apply fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .sidebar-overlay.active {
            @apply block;
            opacity: 1;
        }
        
        .sidebar-collapsed {
            width: 4rem !important;
        }
        
        .sidebar-collapsed .sidebar-text {
            @apply hidden;
        }
        
        .sidebar-collapsed .sidebar-logo {
            @apply justify-center;
        }
        
        .sidebar-collapsed .sidebar-logo-text {
            @apply hidden;
        }
        
        /* Enhanced Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar-mobile {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
            
            .sidebar-mobile.open {
                transform: translateX(0);
            }
            
            /* Touch-friendly menu items */
            .menu-item {
                min-height: 3.5rem;
                padding: 1rem;
            }
            
            /* Mobile search adjustments */
            .mobile-search {
                display: block;
            }
            
            .desktop-search {
                display: none;
            }
        }
        
        /* Tablet Responsive */
        @media (min-width: 768px) and (max-width: 1024px) {
            .sidebar-mobile {
                width: 16rem;
            }
            
            .sidebar-collapsed {
                width: 3.5rem !important;
            }
        }
        
        /* Desktop Responsive */
        @media (min-width: 1024px) {
            .sidebar-mobile {
                position: static;
                transform: none !important;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Mobile menu animations */
        .menu-item {
            transition: all 0.2s ease-in-out;
        }
        
        .menu-item:hover {
            transform: translateX(4px);
        }
        
        /* Touch feedback for mobile */
        @media (max-width: 768px) {
            .menu-item:active {
                background-color: rgba(59, 130, 246, 0.1);
                transform: scale(0.98);
            }
        }
        
        /* Improved mobile header */
        @media (max-width: 768px) {
            .mobile-header {
                padding: 1rem;
            }
            
            .mobile-menu-toggle {
                padding: 0.75rem;
                border-radius: 0.5rem;
            }
        }
        
        /* Icon animations */
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }
        
        @keyframes bounce-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Responsive Sidebar Styles */
        .sidebar-overlay {
            @apply fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .sidebar-overlay.active {
            @apply block;
            opacity: 1;
        }
        
        .sidebar-collapsed {
            width: 4rem !important;
        }
        
        .sidebar-collapsed .sidebar-text {
            @apply hidden;
        }
        
        .sidebar-collapsed .sidebar-logo {
            @apply justify-center;
        }
        
        .sidebar-collapsed .sidebar-logo-text {
            @apply hidden;
        }
        
        /* Enhanced Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar-mobile {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
            
            .sidebar-mobile.open {
                transform: translateX(0);
            }
            
            /* Touch-friendly menu items */
            .menu-item {
                min-height: 3.5rem;
                padding: 1rem;
            }
            
            /* Mobile search adjustments */
            .mobile-search {
                display: block;
            }
            
            .desktop-search {
                display: none;
            }
        }
        
        /* Tablet Responsive */
        @media (min-width: 768px) and (max-width: 1024px) {
            .sidebar-mobile {
                width: 16rem;
            }
            
            .sidebar-collapsed {
                width: 3.5rem !important;
            }
        }
        
        /* Desktop Responsive */
        @media (min-width: 1024px) {
            .sidebar-mobile {
                position: static;
                transform: none !important;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Mobile menu animations */
        .menu-item {
            transition: all 0.2s ease-in-out;
        }
        
        .menu-item:hover {
            transform: translateX(4px);
        }
        
        /* Touch feedback for mobile */
        @media (max-width: 768px) {
            .menu-item:active {
                background-color: rgba(59, 130, 246, 0.1);
                transform: scale(0.98);
            }
        }
        
        /* Improved mobile header */
        @media (max-width: 768px) {
            .mobile-header {
                padding: 1rem;
            }
            
            .mobile-menu-toggle {
                padding: 0.75rem;
                border-radius: 0.5rem;
            }
        }
        
        /* Icon animations */
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }
        
        @keyframes bounce-gentle {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-3px);
            }
        }
        
        .animate-bounce-gentle {
            animation: bounce-gentle 2s ease-in-out infinite;
        }
        
        /* Icon hover effects */
        .icon-hover {
            transition: all 0.3s ease;
        }
        
        .icon-hover:hover {
            transform: scale(1.1);
            filter: brightness(1.2);
        }
        
        /* Backdrop blur for mobile */
        .mobile-backdrop {
        }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ 
    sidebarOpen: false, 
    sidebarCollapsed: false, 
    darkMode: false,
    isMobile: window.innerWidth < 768,
    isTablet: window.innerWidth >= 768 && window.innerWidth < 1024,
    init() {
        this.checkScreenSize();
        window.addEventListener('resize', () => this.checkScreenSize());
        
        // Auto-collapse sidebar on small screens
        if (window.innerWidth < 1024) {
            this.sidebarCollapsed = true;
        }
        
        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.sidebarOpen) {
                this.sidebarOpen = false;
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (this.isMobile && this.sidebarOpen) {
                const sidebar = document.querySelector('aside');
                const menuToggle = e.target.closest('button');
                
                if (!sidebar.contains(e.target) && !menuToggle) {
                    this.sidebarOpen = false;
                }
            }
        });
    },
    checkScreenSize() {
        this.isMobile = window.innerWidth < 768;
        this.isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
        
        // Auto-adjust sidebar based on screen size
        if (window.innerWidth < 768) {
            this.sidebarOpen = false;
        } else if (window.innerWidth >= 1024) {
            this.sidebarOpen = false; // Desktop doesn't need overlay
        }
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    toggleCollapse() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
    }
}">
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" 
         :class="{ 'active': sidebarOpen }" 
         @click="sidebarOpen = false"></div>
    
    <div class="flex min-h-screen">
        <!-- Enhanced Responsive Sidebar -->
        <aside class="sidebar-mobile w-72 bg-white shadow-2xl border-r border-gray-200 flex flex-col fixed md:static inset-y-0 left-0 z-40 transition-all duration-300 ease-in-out" 
               :class="{ 
                   'open': sidebarOpen,
                   'sidebar-collapsed': sidebarCollapsed && !sidebarOpen,
                   '-translate-x-full': !sidebarOpen && window.innerWidth < 768,
                   'translate-x-0': sidebarOpen || window.innerWidth >= 768
               }">
            <!-- Logo/Brand Section -->
            <div class="sidebar-logo p-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-purple-600">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm flex-shrink-0">
                        <i class="fas fa-store text-white text-xl"></i>
                    </div>
                    <div class="sidebar-logo-text">
                        <h1 class="text-xl font-bold text-white">Yakan Admin</h1>
                        <p class="text-xs text-blue-100">E-commerce Platform</p>
                    </div>
                </div>
            </div>

           <!-- Navigation -->
<nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
    <a href="{{ route('admin.dashboard') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
        <i class="fas fa-tachometer-alt w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Dashboard</span>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.orders.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-shopping-bag w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Orders</span>
    </a>

    <a href="{{ route('admin.patterns.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.patterns.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-th-large w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Patterns</span>
    </a>

    <a href="{{ route('admin.custom_orders.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.custom_orders.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-paint-brush w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Custom Orders</span>
    </a>

    <a href="{{ route('admin.products.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.products.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-cube w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Products</span>
    </a>

    <a href="{{ route('admin.coupons.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.coupons.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-ticket-alt w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Coupons</span>
    </a>

    <a href="{{ route('admin.cultural-heritage.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.cultural-heritage.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-book-open w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Cultural Heritage</span>
    </a>

    <!-- <a href="{{ route('admin.inventory.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.inventory.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-warehouse w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Inventory</span>
        <span class="sidebar-text ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">!</span>
    </a> -->

    <a href="{{ route('admin.users.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-user-friends w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Users</span>
    </a>

    <!-- <a href="{{ route('admin.reports.index') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.reports.*') ? 'nav-link-active' : '' }}">
        <i class="fas fa-chart-pie text-gray-400 group-hover:text-gray-600 w-5"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Reports</span>
    </a> -->

    <!-- <a href="{{ route('admin.analytics') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.analytics') ? 'nav-link-active' : '' }}">
        <i class="fas fa-chart-line w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors flex-shrink-0"></i>
        <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Analytics</span>
    </a> -->
    
    <!-- Settings placeholder - can be added later -->
    <div class="pt-4 mt-4 border-t border-gray-200">
        <div class="px-4 py-2">
            <span class="sidebar-text text-xs font-semibold text-gray-400 uppercase tracking-wider">System</span>
        </div>
        <!-- <a href="{{ route('admin.settings.general') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.settings.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-cog text-gray-400 group-hover:text-gray-600 w-5"></i>
            <span class="sidebar-text font-medium text-gray-500 group-hover:text-gray-700">Settings</span>
        </a> -->
    </div>

    <!-- Divider -->
    <div class="pt-4 pb-2">
        <div class="h-px bg-gray-200"></div>
    </div>

    <!-- Quick Actions -->
    <div class="px-4 py-2 mt-4">
        <span class="sidebar-text text-xs font-semibold text-gray-400 uppercase tracking-wider">Quick Actions</span>
    </div>
    <a href="{{ route('admin.products.create') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-100 group">
        <i class="fas fa-plus-circle text-gray-400 group-hover:text-green-600 w-4"></i>
        <span class="sidebar-text text-sm font-medium text-gray-600 group-hover:text-gray-900">Add Product</span>
    </a>
    <!-- <a href="{{ route('admin.inventory.create') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-100 group">
            <i class="fas fa-plus w-5 h-5 text-gray-400 group-hover:text-green-500 transition-colors flex-shrink-0"></i>
            <span class="sidebar-text font-medium text-gray-700 group-hover:text-gray-900">Add Inventory</span>
        </a> -->
    <a href="{{ route('admin.users.create') }}" class="menu-item nav-link flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-100 group">
        <i class="fas fa-user-plus text-gray-400 group-hover:text-blue-600 w-4"></i>
        <span class="sidebar-text text-sm font-medium text-gray-600 group-hover:text-gray-900">Add User</span>
    </a>
</nav>

            <!-- User Profile & Logout Section -->
<div class="p-4 border-t border-gray-200">
    <!-- Desktop Collapse Toggle -->
    <button @click="sidebarCollapsed = !sidebarCollapsed" 
            class="hidden md:flex items-center justify-center w-full p-2 mb-3 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            title="Toggle Sidebar">
        <i class="fas fa-chevron-left" :class="{ 'fa-chevron-right': sidebarCollapsed, 'fa-chevron-left': !sidebarCollapsed }"></i>
    </button>
    
    <div class="bg-gray-50 rounded-lg p-3 mb-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-white">AD</span>
            </div>
            <div class="sidebar-text flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()?->name ?? 'Admin User' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()?->email ?? 'admin@example.com' }}</p>
            </div>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 transition-all group">
            <i class="fas fa-sign-out-alt w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
            <span class="font-medium">Logout</span>
        </button>
    </form>
</div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ease-in-out"
              :class="{ 'ml-0': sidebarCollapsed && window.innerWidth >= 768, 'md:ml-0': !sidebarCollapsed && window.innerWidth >= 768 }">
            <!-- Top Header Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 mobile-header transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Mobile Menu Toggle -->
                        <button class="mobile-menu-toggle md:hidden inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" 
                                @click="toggleSidebar()"
                                :class="{ 'text-blue-600': sidebarOpen }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Tablet Sidebar Toggle -->
                        <button class="hidden md:inline-flex lg:hidden items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" 
                                @click="toggleCollapse()"
                                :class="{ 'text-blue-600': sidebarCollapsed }">
                            <i class="fas fa-bars w-5 h-5"></i>
                        </button>
                        
                        <!-- Desktop Sidebar Toggle -->
                        <button class="hidden lg:inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" 
                                @click="toggleCollapse()"
                                :class="{ 'text-blue-600': sidebarCollapsed }">
                            <i class="fas fa-bars w-5 h-5"></i>
                        </button>
                        
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
                            <p class="text-sm text-gray-500 mt-1 hidden sm:block">Welcome back to your admin panel</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <!-- Search Bar (Desktop) -->
                        <div class="desktop-search hidden lg:flex items-center">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Search..." 
                                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Mobile Search Button -->
                        <button class="mobile-search lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                @click="$refs.mobileSearchModal.show()">
                            <i class="fas fa-search w-5 h-5"></i>
                        </button>
                        
                        <!-- Notification Bell -->
                        @include('components.admin-notification-dropdown')
                        
                        <!-- User Menu (Mobile) -->
                        <div class="md:hidden">
                            <button class="flex items-center space-x-2 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                    @click="$refs.mobileUserMenu.toggle()">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">AD</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-6">
                <!-- Session Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6 shadow-sm flex items-start">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium">Success</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 shadow-sm flex items-start">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-medium">Error</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <div>
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    
    <!-- Mobile Search Modal -->
    <div x-ref="mobileSearchModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden lg:hidden" @click="$event.target === $el && $el.hide()">
        <div class="bg-white p-4" @click.stop>
            <div class="flex items-center space-x-3 mb-4">
                <button @click="$refs.mobileSearchModal.hide()" class="p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
                <h3 class="text-lg font-semibold">Search</h3>
            </div>
            <div class="relative">
                <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Mobile User Menu -->
    <div x-ref="mobileUserMenu" class="fixed top-16 right-4 bg-white rounded-lg shadow-lg border border-gray-200 p-2 z-50 hidden md:hidden" @click.away="$el.hide()">
        <div class="py-2">
            <div class="px-4 py-2 border-b border-gray-200">
                <p class="text-sm font-medium text-gray-900">{{ auth()->user()?->name ?? 'Admin User' }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()?->email ?? 'admin@example.com' }}</p>
            </div>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Profile</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Settings</a>
            <form method="POST" action="{{ route('admin.logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Logout</button>
            </form>
        </div>
    </div>

    <!-- Responsive Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle window resize events for manual adjustments if needed
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    // Any manual resize adjustments can go here
                    console.log('Window resized to:', window.innerWidth);
                }, 250);
            });
            
            // Initialize mobile modals
            const mobileSearchModal = document.querySelector('[x-ref="mobileSearchModal"]');
            const mobileUserMenu = document.querySelector('[x-ref="mobileUserMenu"]');
            
            if (mobileSearchModal) {
                mobileSearchModal.hide = function() {
                    this.classList.add('hidden');
                };
                mobileSearchModal.show = function() {
                    this.classList.remove('hidden');
                };
            }
            
            if (mobileUserMenu) {
                mobileUserMenu.hide = function() {
                    this.classList.add('hidden');
                };
                mobileUserMenu.show = function() {
                    this.classList.remove('hidden');
                };
                mobileUserMenu.toggle = function() {
                    this.classList.toggle('hidden');
                };
            }
        });
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>