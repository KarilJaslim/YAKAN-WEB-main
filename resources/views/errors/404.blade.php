@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-6xl font-extrabold text-gray-900">404</h1>
        <p class="mt-4 text-lg text-gray-700">Page not found</p>
        <p class="mt-2 text-sm text-gray-500">The page you are looking for doesn't exist or has been moved.</p>
        <div class="mt-8">
            <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                Go back home
            </a>
        </div>
    </div>
</div>
@endsection
