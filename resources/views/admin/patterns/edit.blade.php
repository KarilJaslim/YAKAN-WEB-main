@extends('layouts.admin')

@section('title', 'Edit Pattern')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-maroon-700 to-maroon-800 shadow-2xl" style="background: linear-gradient(to right, #800000, #600000);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center">
                <a href="{{ route('admin.patterns.index') }}" class="text-maroon-100 hover:text-white mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-white">Edit Pattern</h1>
                    <p class="text-maroon-100 mt-2">Update pattern details and media</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <form method="POST" action="{{ route('admin.patterns.update', $pattern) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-black text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pattern Name *</label>
                        <input type="text" name="name" value="{{ old('name', $pattern->name) }}" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500" />
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500">
                            <option value="">Select Category</option>
                            <option value="traditional" {{ old('category', $pattern->category) == 'traditional' ? 'selected' : '' }}>Traditional</option>
                            <option value="modern" {{ old('category', $pattern->category) == 'modern' ? 'selected' : '' }}>Modern</option>
                            <option value="contemporary" {{ old('category', $pattern->category) == 'contemporary' ? 'selected' : '' }}>Contemporary</option>
                        </select>
                        @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level *</label>
                        <select name="difficulty_level" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500">
                            <option value="">Select Difficulty</option>
                            <option value="simple" {{ old('difficulty_level', $pattern->difficulty_level) == 'simple' ? 'selected' : '' }}>Simple</option>
                            <option value="medium" {{ old('difficulty_level', $pattern->difficulty_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="complex" {{ old('difficulty_level', $pattern->difficulty_level) == 'complex' ? 'selected' : '' }}>Complex</option>
                        </select>
                        @error('difficulty_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base Color</label>
                        <input type="text" name="base_color" value="{{ old('base_color', $pattern->base_color) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500" />
                        @error('base_color') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500">{{ old('description', $pattern->description) }}</textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Pricing & Settings -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-black text-gray-900 mb-4">Pricing & Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base Price Multiplier</label>
                        <input type="number" step="0.01" min="0" max="10" name="base_price_multiplier" value="{{ old('base_price_multiplier', $pattern->base_price_multiplier) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500" />
                        @error('base_price_multiplier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" value="1" class="rounded" @checked(old('is_active', $pattern->is_active)) />
                        <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-black text-gray-900 mb-4">Tags</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($tags as $tag)
                        <label class="flex items-center">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', $pattern->tags->pluck('id')->toArray()))) class="rounded mr-2" />
                            <span class="text-sm">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Existing Media -->
            @if($pattern->media->isNotEmpty())
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-black text-gray-900 mb-4">Current Media</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($pattern->media as $media)
                            <div class="relative group">
                                <img src="{{ $media->url }}" alt="{{ $media->alt_text ?? $pattern->name }}" class="w-full h-32 object-cover rounded-lg" />
                                <form action="{{ route('admin.patterns.destroy', $pattern) }}" method="POST" class="absolute top-2 right-2" onsubmit="return confirm('Remove this media?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Add New Media -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-black text-gray-900 mb-4">Add New Media</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                    <p class="text-sm text-gray-500">PNG, JPG, WEBP up to 5MB each</p>
                    <input type="file" name="media[]" multiple accept="image/*" class="hidden" id="media-upload" />
                    <button type="button" onclick="document.getElementById('media-upload').click()" class="mt-4 px-4 py-2 bg-maroon-600 text-white rounded-lg hover:bg-maroon-700 transition-colors">Select Files</button>
                </div>
                <div id="media-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.patterns.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 font-black rounded-lg hover:bg-gray-300 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-maroon-600 text-white font-black rounded-lg hover:bg-maroon-700 transition-colors">Update Pattern</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('media-upload').addEventListener('change', function(e) {
    const preview = document.getElementById('media-preview');
    preview.innerHTML = '';
    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg" />
                <input type="text" name="media_alt[${index}]" placeholder="Alt text" class="mt-2 w-full px-2 py-1 border rounded text-sm" />
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
