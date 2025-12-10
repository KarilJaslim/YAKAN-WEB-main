@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-center">Forgot Password</h2>
    <p class="mb-6 text-sm text-gray-600 text-center">
        Enter your account email and we'll send you a link to reset your password.
    </p>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block mb-1 font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-500">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Email Password Reset Link
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login.user.form') }}" class="text-blue-600 hover:underline">Back to Login</a>
        </div>
    </form>
</div>
@endsection
