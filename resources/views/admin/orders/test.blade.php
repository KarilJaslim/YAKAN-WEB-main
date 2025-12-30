@extends('admin.layout')

@section('title', 'Test Orders Page')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Test Orders Page</h1>
    <p>This is a minimal test to check if the layout works.</p>
    <p>Orders count: {{ count($orders) }}</p>
</div>
@endsection
