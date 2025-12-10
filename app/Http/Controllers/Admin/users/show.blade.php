@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6">
    <a href="{{ route('admin.users.index') }}" class="mb-4 inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        ← Back to Users
    </a>

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">User #{{ $user->id }}</h1>

        <div class="space-y-2">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Created At:</strong> {{ $user->created_at->format('M d, Y h:i A') }}</p>
            <p><strong>Updated At:</strong> {{ $user->updated_at->format('M d, Y h:i A') }}</p>
        </div>

        <div class="mt-6">
            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this user?')">
                    Delete User
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
