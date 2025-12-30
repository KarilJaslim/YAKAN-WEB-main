@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="text-4xl">🧪</span>
                        Payment Sandbox
                    </h1>
                    <p class="text-gray-600 mt-2">Test payment flows in a safe environment</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-semibold">
                        Sandbox Mode Active
                    </span>
                </div>
            </div>
        </div>

        <!-- Test Scenarios -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span>🎭</span>
                Test Scenarios
            </h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="border border-green-200 rounded-xl p-4 bg-green-50">
                    <h3 class="font-semibold text-green-900">✅ Success</h3>
                    <p class="text-sm text-gray-600 mt-1">Payment completes successfully</p>
                    <div class="mt-3">
                        <button class="px-3 py-1 bg-green-600 text-white rounded text-sm">Test</button>
                    </div>
                </div>
                
                <div class="border border-red-200 rounded-xl p-4 bg-red-50">
                    <h3 class="font-semibold text-red-900">❌ Failed</h3>
                    <p class="text-sm text-gray-600 mt-1">Payment fails</p>
                    <div class="mt-3">
                        <button class="px-3 py-1 bg-red-600 text-white rounded text-sm">Test</button>
                    </div>
                </div>
                
                <div class="border border-yellow-200 rounded-xl p-4 bg-yellow-50">
                    <h3 class="font-semibold text-yellow-900">⏳ Pending</h3>
                    <p class="text-sm text-gray-600 mt-1">Payment processing</p>
                    <div class="mt-3">
                        <button class="px-3 py-1 bg-yellow-600 text-white rounded text-sm">Test</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span>💳</span>
                Payment Methods
            </h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900">GCash</h3>
                    <p class="text-sm text-gray-600">Mobile wallet payments</p>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900">Online Banking</h3>
                    <p class="text-sm text-gray-600">Direct bank transfers</p>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900">Bank Transfer</h3>
                    <p class="text-sm text-gray-600">Manual bank deposits</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
