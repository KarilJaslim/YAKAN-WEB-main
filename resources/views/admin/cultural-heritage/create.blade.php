@extends('layouts.admin')

@section('title', 'Add Cultural Heritage Content')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Content</h1>
            <p class="text-gray-600 mt-1">Create new cultural heritage content</p>
        </div>
        <a href="{{ route('admin.cultural-heritage.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.cultural-heritage.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8 space-y-6">
        @csrf

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Summary -->
        <div>
            <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">Summary</label>
            <textarea name="summary" id="summary" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('summary') border-red-500 @enderror">{{ old('summary') }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Brief description (max 500 characters)</p>
            @error('summary')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content -->
        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
            <textarea name="content" id="content" rows="15" required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-sm @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Full content (supports HTML)</p>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Upload -->
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
            <div class="space-y-4">
                <label id="uploadArea" class="flex flex-col items-center px-4 py-6 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <span class="text-sm text-gray-600">Click to upload image</span>
                    <span class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</span>
                    <input type="file" name="image" id="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                </label>
                <div id="imagePreview" class="hidden">
                    <div class="relative inline-block">
                        <img src="" alt="Preview" class="w-full max-w-md h-64 object-cover rounded-lg border-2 border-gray-300">
                        <button type="button" onclick="removeImage()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                        <p id="fileName" class="text-sm text-gray-600 mt-2"></p>
                    </div>
                </div>
            </div>
            @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category & Order Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select name="category" id="category" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('category') border-red-500 @enderror">
                    <option value="">Select Category</option>
                    <option value="history" {{ old('category') == 'history' ? 'selected' : '' }}>History</option>
                    <option value="tradition" {{ old('category') == 'tradition' ? 'selected' : '' }}>Tradition</option>
                    <option value="culture" {{ old('category') == 'culture' ? 'selected' : '' }}>Culture</option>
                    <option value="art" {{ old('category') == 'art' ? 'selected' : '' }}>Art</option>
                    <option value="crafts" {{ old('category') == 'crafts' ? 'selected' : '' }}>Crafts</option>
                    <option value="language" {{ old('category') == 'language' ? 'selected' : '' }}>Language</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Order -->
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('order') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Author & Published Date Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Author -->
            <div>
                <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                <input type="text" name="author" id="author" value="{{ old('author') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('author') border-red-500 @enderror">
                @error('author')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Published Date -->
            <div>
                <label for="published_date" class="block text-sm font-medium text-gray-700 mb-2">Published Date</label>
                <input type="date" name="published_date" id="published_date" value="{{ old('published_date', date('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 @error('published_date') border-red-500 @enderror">
                @error('published_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Published Status -->
        <div class="flex items-center">
            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}
                   class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
            <label for="is_published" class="ml-2 text-sm font-medium text-gray-700">
                Publish immediately
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.cultural-heritage.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Create Content
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function previewImage(event) {
    const uploadArea = document.getElementById('uploadArea');
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    const fileName = document.getElementById('fileName');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            fileName.textContent = 'Selected: ' + file.name;
            uploadArea.classList.add('hidden');
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    const uploadArea = document.getElementById('uploadArea');
    const preview = document.getElementById('imagePreview');
    const input = document.getElementById('image');
    const img = preview.querySelector('img');
    const fileName = document.getElementById('fileName');
    
    // Clear the file input
    input.value = '';
    img.src = '';
    fileName.textContent = '';
    
    // Show upload area, hide preview
    uploadArea.classList.remove('hidden');
    preview.classList.add('hidden');
}
</script>
@endpush
