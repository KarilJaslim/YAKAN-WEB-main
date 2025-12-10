@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-center">Reset Password</h2>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-4">
            <label for="email" class="block mb-1 font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="password" class="block mb-1 font-medium">New Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-500">
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block mb-1 font-medium">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-500">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Reset Password
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login.user.form') }}" class="text-blue-600 hover:underline">Back to Login</a>
        </div>
    </form>
</div>
@endsection
