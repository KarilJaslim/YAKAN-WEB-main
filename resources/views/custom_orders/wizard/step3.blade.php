@extends('layouts.app')

@section('title', 'Order Details - Custom Order')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50">
    <!-- Enhanced Progress Bar -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-center space-x-6">
                <div class="flex items-center group cursor-pointer">
                    <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg transform transition-all duration-300 group-hover:scale-110">
                        ✓
                    </div>
                    <span class="ml-3 font-bold text-green-600">Fabric</span>
                </div>
                <div class="w-20 h-1 bg-green-600 rounded-full"></div>
                <div class="flex items-center group cursor-pointer">
                    <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg transform transition-all duration-300 group-hover:scale-110">
                        ✓
                    </div>
                    <span class="ml-3 font-bold text-green-600">Design</span>
                </div>
                <div class="w-20 h-1 bg-gradient-to-r from-green-600 to-purple-600 rounded-full"></div>
                <div class="flex items-center group cursor-pointer">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg transform transition-all duration-300 group-hover:scale-110">
                            3
                        </div>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                    <span class="ml-3 font-bold text-purple-600">Details</span>
                </div>
                <div class="w-20 h-1 bg-gray-300 rounded-full"></div>
                <div class="flex items-center group cursor-pointer opacity-60">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 group-hover:scale-110">
                        4
                    </div>
                    <span class="ml-3 font-medium text-gray-500">Review</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Header with Yakan Pattern Background -->
    <div class="bg-white border-b border-gray-200 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="yakanHeaderPattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                        <circle cx="50" cy="50" r="35" fill="#8B0000" stroke="#ffffff" stroke-width="1"/>
                        <circle cx="50" cy="50" r="25" fill="#FFD700" stroke="#ffffff" stroke-width="0.5"/>
                        <circle cx="50" cy="50" r="12" fill="#8B0000"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#yakanHeaderPattern)"/>
            </svg>
        </div>
        <div class="container mx-auto px-4 py-12 relative">
            <div class="text-center">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-4">Order Details</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Tell us about your custom order requirements and preferences</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Order Form -->
    <div class="container mx-auto px-4 py-8">
        <form id="orderDetailsForm" method="POST" action="{{ route('custom_orders.store.step3') }}" class="max-w-4xl mx-auto">
            @csrf
            
            <!-- Enhanced Customer Information -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-8">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900">Order Information</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Order Name *
                        </label>
                        <div class="relative">
                            <input type="text" name="order_name" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                   placeholder="Give your order a memorable name">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                            Size *
                        </label>
                        <div class="relative">
                            <select name="size" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                <option value="">Select size</option>
                                <option value="xs">XS - Extra Small</option>
                                <option value="s">S - Small</option>
                                <option value="m">M - Medium</option>
                                <option value="l">L - Large</option>
                                <option value="xl">XL - Extra Large</option>
                                <option value="xxl">XXL - Double Extra Large</option>
                                <option value="custom">📏 Custom Size</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Priority Level *
                        </label>
                        <div class="relative">
                            <select name="priority" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                <option value="">Select priority</option>
                                <option value="standard">🕐 Standard (7-10 days)</option>
                                <option value="priority">⚡ Priority (5-7 days)</option>
                                <option value="express">🚀 Express (3-5 days)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Customer Name
                        </label>
                        <div class="relative">
                            <input type="text" name="customer_name" 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                   placeholder="Full name for delivery">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contact Email
                        </label>
                        <div class="relative">
                            <input type="email" name="customer_email" 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                   placeholder="your.email@example.com">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l.894 2.682A1 1 0 0011.053 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                            </svg>
                            Contact Phone
                        </label>
                        <input type="text" name="customer_phone"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                               placeholder="09XX-XXX-XXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l.894 2.682A1 1 0 0011.053 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                            </svg>
                            Delivery Option
                        </label>
                        <div class="flex flex-col space-y-2">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="delivery_type" value="delivery" class="text-purple-600 focus:ring-purple-500" checked>
                                <span class="text-sm text-gray-700">Delivery</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="delivery_type" value="pickup" class="text-purple-600 focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Store Pickup</span>
                            </label>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657L12 22l5.657-5.343z"/>
                                </svg>
                                Delivery Address (for Delivery)
                            </label>
                            <textarea name="delivery_address" rows="3"
                                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                      placeholder="House / Unit, Street, Barangay, City, Province, ZIP"></textarea>
                            <p class="mt-1 text-xs text-gray-500">If you choose Delivery, please provide your complete address. For Store Pickup, you may leave this blank.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Order Description
                    </label>
                    <textarea name="description" rows="4" 
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                              placeholder="Describe any specific requirements or preferences for your custom order..."></textarea>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Special Instructions
                    </label>
                    <textarea name="special_instructions" rows="3" 
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                              placeholder="Any special handling instructions or additional notes..."></textarea>
                </div>
            </div>

            <!-- Enhanced Measurements & Specifications -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-8">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900">Measurements & Specifications</h3>
                </div>
                
                @if(isset($product) && str_contains(strtolower($product->name), 'cap'))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Head Size
                            </label>
                            <div class="relative">
                                <select name="head_size" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select size</option>
                                    <option value="small">🔹 Small (54-56 cm)</option>
                                    <option value="medium">🔹 Medium (57-59 cm)</option>
                                    <option value="large">🔹 Large (60-62 cm)</option>
                                    <option value="xlarge">🔹 X-Large (63-65 cm)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Fit Preference
                            </label>
                            <div class="relative">
                                <select name="fit_preference" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select fit</option>
                                    <option value="snug">🔹 Snug Fit</option>
                                    <option value="regular">🔹 Regular Fit</option>
                                    <option value="loose">🔹 Loose Fit</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Cap Style
                            </label>
                            <div class="relative">
                                <select name="cap_style" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select style</option>
                                    <option value="fitted">🔹 Fitted</option>
                                    <option value="adjustable">🔹 Adjustable</option>
                                    <option value="snapback">🔹 Snapback</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(isset($product) && (str_contains(strtolower($product->name), 'shirt') || str_contains(strtolower($product->name), 'hoodie')))
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                Clothing Size *
                            </label>
                            <div class="relative">
                                <select name="clothing_size" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select size</option>
                                    <option value="xs">XS - Extra Small</option>
                                    <option value="s">S - Small</option>
                                    <option value="m">M - Medium</option>
                                    <option value="l">L - Large</option>
                                    <option value="xl">XL - Extra Large</option>
                                    <option value="xxl">XXL - Double Extra Large</option>
                                    <option value="xxxl">XXXL - Triple Extra Large</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Chest (cm)
                            </label>
                            <div class="relative">
                                <input type="number" name="chest_measurement" 
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                       placeholder="90-120">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Length (cm)
                            </label>
                            <div class="relative">
                                <input type="number" name="length_measurement"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                       placeholder="60-80">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Sleeve (cm)
                            </label>
                            <div class="relative">
                                <input type="number" name="sleeve_measurement"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                       placeholder="20-40">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(isset($product) && str_contains(strtolower($product->name), 'bag'))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Bag Size
                            </label>
                            <div class="relative">
                                <select name="bag_size" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select size</option>
                                    <option value="small">🔹 Small (25x30 cm)</option>
                                    <option value="medium">🔹 Medium (35x40 cm)</option>
                                    <option value="large">🔹 Large (45x50 cm)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Strap Length
                            </label>
                            <div class="relative">
                                <select name="strap_length" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select length</option>
                                    <option value="short">🔹 Short (50 cm)</option>
                                    <option value="medium">🔹 Medium (75 cm)</option>
                                    <option value="long">🔹 Long (100 cm)</option>
                                    <option value="adjustable">🔹 Adjustable</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Closure Type
                            </label>
                            <div class="relative">
                                <select name="closure_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 appearance-none">
                                    <option value="">Select closure</option>
                                    <option value="zipper">🔹 Zipper</option>
                                    <option value="magnetic">🔹 Magnetic</option>
                                    <option value="open">🔹 Open Top</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Enhanced Optional Add-ons -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-8">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900">Optional Add-ons</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-all duration-300 transform hover:scale-102">
                        <input type="checkbox" name="addons[]" value="priority_production" class="mr-4 text-purple-600 w-5 h-5">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg group-hover:text-purple-600 transition-colors">⚡ Priority Production</div>
                            <div class="text-sm text-gray-600 mt-1">Get your order in 3-5 days (+₱500)</div>
                        </div>
                        <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs px-2 py-1 rounded-full font-semibold">Popular</span>
                        </div>
                    </label>
                    
                    <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-all duration-300 transform hover:scale-102">
                        <input type="checkbox" name="addons[]" value="gift_wrapping" class="mr-4 text-purple-600 w-5 h-5">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg group-hover:text-purple-600 transition-colors">🎁 Gift Wrapping</div>
                            <div class="text-sm text-gray-600 mt-1">Premium gift packaging (+₱150)</div>
                        </div>
                    </label>
                    
                    <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-all duration-300 transform hover:scale-102">
                        <input type="checkbox" name="addons[]" value="extra_patterns" class="mr-4 text-purple-600 w-5 h-5">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg group-hover:text-purple-600 transition-colors">🎨 Extra Pattern Set</div>
                            <div class="text-sm text-gray-600 mt-1">Additional pattern variations (+₱200)</div>
                        </div>
                    </label>
                    
                    <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-all duration-300 transform hover:scale-102">
                        <input type="checkbox" name="addons[]" value="insurance" class="mr-4 text-purple-600 w-5 h-5">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg group-hover:text-purple-600 transition-colors">🛡️ Shipping Insurance</div>
                            <div class="text-sm text-gray-600 mt-1">Full coverage for lost/damaged items (+₱100)</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Enhanced Production Timeline -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-8">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900">Production Timeline</h3>
                </div>
                
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border-2 border-purple-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-bold text-purple-900">Estimated Production Time</h4>
                            <p class="text-sm text-purple-700 mt-2">Standard production takes 7-10 business days. Priority production (if selected) takes 3-5 business days.</p>
                            <p class="text-sm text-purple-700 mt-2">You'll receive email updates at each stage of production.</p>
                            <div class="mt-4 flex items-center space-x-6 text-sm text-purple-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Quality craftsmanship
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Regular updates
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Satisfaction guaranteed
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Navigation -->
            <div class="flex justify-between items-center">
                @if(isset($product))
                    {{-- Product flow: go back to product customization --}}
                    <a href="{{ route('custom_orders.create.product.customize') }}" class="group flex items-center text-gray-600 hover:text-purple-600 font-bold transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Design
                    </a>
                @else
                    {{-- Fabric flow: go back to pattern selection --}}
                    <a href="{{ route('custom_orders.create.pattern') }}" class="group flex items-center text-gray-600 hover:text-purple-600 font-bold transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Design
                    </a>
                @endif
                
                <button type="submit" class="group relative px-12 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105 hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <span class="flex items-center">
                        Continue to Review
                        <svg class="w-5 h-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form validation
    const form = document.getElementById('orderDetailsForm');
    
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500', 'bg-red-50');
                
                // Show enhanced error message
                const errorMsg = document.createElement('div');
                errorMsg.className = 'mt-2 p-3 bg-red-100 border border-red-300 rounded-lg text-red-700 text-sm font-medium';
                errorMsg.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        This field is required
                    </div>
                `;
                field.parentNode.appendChild(errorMsg);
                
                setTimeout(() => {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    errorMsg.remove();
                }, 3000);
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields', 'warning');
            return false;
        }
        
        // Show loading state
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...</span>';
        submitBtn.disabled = true;
        
        showNotification('Processing your order details...', 'info');
    });
    
    // Enhanced add-on selection
    const addonCheckboxes = document.querySelectorAll('input[name="addons[]"]');
    addonCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updatePricingSummary();
            
            // Visual feedback
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('border-purple-500', 'bg-purple-50', 'ring-4', 'ring-purple-200');
            } else {
                label.classList.remove('border-purple-500', 'bg-purple-50', 'ring-4', 'ring-purple-200');
            }
        });
    });
    
    function updatePricingSummary() {
        // This would update a pricing summary if we had one
        console.log('Updating pricing summary...');
    }
    
    // Add input animations
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.add('ring-2', 'ring-purple-500', 'ring-offset-2');
        });
        
        input.addEventListener('blur', function() {
            this.classList.remove('ring-2', 'ring-purple-500', 'ring-offset-2');
        });
    });
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        warning: 'bg-yellow-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.classList.add(...colors[type].split(' '));
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.addon-option {
    transition: all 0.3s ease;
}

.addon-option:hover {
    box-shadow: 0 10px 30px rgba(147, 51, 234, 0.1);
}

.addon-option.selected {
    border-color: #9333ea;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(59, 130, 246, 0.1));
    box-shadow: 0 0 20px rgba(147, 51, 234, 0.2);
}

input[type="checkbox"]:checked + .addon-option {
    border-color: #9333ea;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(59, 130, 246, 0.1));
}
</style>
@endpush

@endsection
