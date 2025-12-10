@extends('layouts.admin')
@section('title', 'Add Product')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded-lg mt-6">
    <h2 class="text-2xl font-bold mb-6">Add Product</h2>

    @if ($errors->any())
        <div class="mb-4 p-4 border border-red-200 bg-red-50 rounded">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Product Name -->
        <div>
            <label class="block font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-600" required>
        </div>

        <!-- Category -->
        <div>
            <label class="block font-medium text-gray-700 mb-2">Category</label>
            <div class="flex gap-2">
                <select name="category_id" id="categorySelect"
                    class="border rounded px-3 py-2 flex-1 focus:outline-none focus:ring-2 focus:ring-red-600"
                    style="border-color: #800000;">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button type="button" onclick="toggleNewCategory()" 
                    class="px-4 py-2 text-white rounded hover:opacity-90 transition-colors whitespace-nowrap"
                    style="background-color: #800000;">
                    <i class="fas fa-plus mr-1"></i> New
                </button>
            </div>
            
            <!-- New Category Input (Hidden by default) -->
            <div id="newCategoryDiv" class="mt-3 hidden">
                <div class="p-4 border-2 rounded-lg" style="border-color: #800000; background-color: #fff5f5;">
                    <label class="block font-medium text-gray-700 mb-2">New Category Name</label>
                    <div class="flex gap-2">
                        <input type="text" id="newCategoryInput" placeholder="Enter category name"
                            class="border rounded px-3 py-2 flex-1 focus:outline-none focus:ring-2"
                            style="border-color: #800000;">
                        <button type="button" onclick="addNewCategory()" 
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                            <i class="fas fa-check"></i> Add
                        </button>
                        <button type="button" onclick="toggleNewCategory()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Available Categories -->
            <div class="mt-3">
                <p class="text-sm text-gray-600 mb-2">Available Categories:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $category)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white cursor-pointer hover:opacity-80 transition-opacity"
                              style="background-color: #800000;"
                              onclick="document.getElementById('categorySelect').value='{{ $category->id }}'">
                            {{ $category->name }}
                        </span>
                    @endforeach
                    @if($categories->isEmpty())
                        <span class="text-sm text-gray-500 italic">No categories yet. Create one above!</span>
                    @endif
                </div>
            </div>
        </div>

        <script>
        function toggleNewCategory() {
            const div = document.getElementById('newCategoryDiv');
            const input = document.getElementById('newCategoryInput');
            div.classList.toggle('hidden');
            if (!div.classList.contains('hidden')) {
                input.focus();
            } else {
                input.value = '';
            }
        }

        function addNewCategory() {
            const input = document.getElementById('newCategoryInput');
            const categoryName = input.value.trim();
            
            if (!categoryName) {
                alert('Please enter a category name');
                return;
            }

            // Send AJAX request to create category
            fetch('{{ route("admin.categories.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: categoryName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new option to select
                    const select = document.getElementById('categorySelect');
                    const option = new Option(data.category.name, data.category.id, true, true);
                    select.add(option);
                    
                    // Show success message
                    alert('Category "' + data.category.name + '" created successfully!');
                    
                    // Reset and hide form
                    input.value = '';
                    toggleNewCategory();
                    
                    // Reload page to update category pills
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to create category'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to create category. Please try again.');
            });
        }

        // Allow Enter key to submit new category
        document.getElementById('newCategoryInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addNewCategory();
            }
        });
        </script>

        <!-- Price -->
        <div>
            <label class="block font-medium text-gray-700">Price (₱)</label>
            <input type="number" name="price" value="{{ old('price') }}"
                class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-600" step="0.01"
                placeholder="0.00" required>
        </div>

        <!-- Stock -->
        <div>
            <label class="block font-medium text-gray-700">Stock</label>
            <input type="number" name="stock" value="{{ old('stock', 0) }}"
                class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-600" min="0"
                required>
        </div>

        <!-- Available Sizes -->
        <div class="border-2 rounded-lg p-4" style="border-color: #800000; background-color: #fff9f9;">
            <label class="block font-bold text-gray-900 mb-2">
                <i class="fas fa-ruler mr-2" style="color: #800000;"></i>Available Sizes
            </label>
            <p class="text-sm text-gray-600 mb-3">
                Add size options for this product. Examples: S, M, L, XL (for clothing) | 6, 7, 8, 9 (for shoes) | 1m, 2m, 3m (for fabrics)
            </p>
            
            <div class="flex gap-2 mb-3">
                <input type="text" id="sizeInput" placeholder="e.g., Small, 7, 2m"
                    class="border rounded px-3 py-2 flex-1 focus:outline-none focus:ring-2"
                    style="border-color: #800000;"
                    onkeypress="if(event.key === 'Enter') { event.preventDefault(); addSize(); }">
                <button type="button" onclick="addSize()" 
                    class="px-4 py-2 text-white rounded hover:opacity-90 transition-colors whitespace-nowrap"
                    style="background-color: #800000;">
                    <i class="fas fa-plus mr-1"></i> Add Size
                </button>
            </div>
            
            <div id="sizesList" class="flex flex-wrap gap-2 min-h-[40px] p-2 border rounded bg-white">
                <span class="text-sm text-gray-400 italic" id="sizesPlaceholder">No sizes added yet</span>
            </div>
            <input type="hidden" name="available_sizes" id="sizesData" value="">
        </div>

        <!-- Available Colors -->
        <div class="border-2 rounded-lg p-4" style="border-color: #800000; background-color: #fff9f9;">
            <label class="block font-bold text-gray-900 mb-2">
                <i class="fas fa-palette mr-2" style="color: #800000;"></i>Available Colors
            </label>
            <p class="text-sm text-gray-600 mb-3">
                Add color options for this product. Examples: Red, Blue, Green, Multicolor
            </p>
            
            <div class="flex gap-2 mb-3">
                <input type="text" id="colorInput" placeholder="e.g., Red, Blue, Multicolor"
                    class="border rounded px-3 py-2 flex-1 focus:outline-none focus:ring-2"
                    style="border-color: #800000;"
                    onkeypress="if(event.key === 'Enter') { event.preventDefault(); addColor(); }">
                <button type="button" onclick="addColor()" 
                    class="px-4 py-2 text-white rounded hover:opacity-90 transition-colors whitespace-nowrap"
                    style="background-color: #800000;">
                    <i class="fas fa-plus mr-1"></i> Add Color
                </button>
            </div>
            
            <div id="colorsList" class="flex flex-wrap gap-2 min-h-[40px] p-2 border rounded bg-white">
                <span class="text-sm text-gray-400 italic" id="colorsPlaceholder">No colors added yet</span>
            </div>
            <input type="hidden" name="available_colors" id="colorsData" value="">
        </div>

        <script>
        let sizes = [];
        let colors = [];
        
        function addSize() {
            const input = document.getElementById('sizeInput');
            const size = input.value.trim();
            
            if (!size) {
                alert('Please enter a size');
                return;
            }
            
            if (sizes.includes(size)) {
                alert('This size already exists');
                return;
            }
            
            sizes.push(size);
            input.value = '';
            updateSizesList();
        }
        
        function removeSize(size) {
            sizes = sizes.filter(s => s !== size);
            updateSizesList();
        }
        
        function updateSizesList() {
            const container = document.getElementById('sizesList');
            const placeholder = document.getElementById('sizesPlaceholder');
            const hiddenInput = document.getElementById('sizesData');
            
            if (sizes.length === 0) {
                placeholder.classList.remove('hidden');
                container.innerHTML = '<span class="text-sm text-gray-400 italic" id="sizesPlaceholder">No sizes added yet</span>';
            } else {
                container.innerHTML = sizes.map(size => `
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium text-white gap-2"
                          style="background-color: #800000;">
                        ${size}
                        <button type="button" onclick="removeSize('${size}')" 
                                class="hover:bg-red-800 rounded-full w-4 h-4 flex items-center justify-center">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>
                `).join('');
            }
            
            hiddenInput.value = JSON.stringify(sizes);
        }
        
        function addColor() {
            const input = document.getElementById('colorInput');
            const color = input.value.trim();
            
            if (!color) {
                alert('Please enter a color');
                return;
            }
            
            if (colors.includes(color)) {
                alert('This color already exists');
                return;
            }
            
            colors.push(color);
            input.value = '';
            updateColorsList();
        }
        
        function removeColor(color) {
            colors = colors.filter(c => c !== color);
            updateColorsList();
        }
        
        function updateColorsList() {
            const container = document.getElementById('colorsList');
            const placeholder = document.getElementById('colorsPlaceholder');
            const hiddenInput = document.getElementById('colorsData');
            
            if (colors.length === 0) {
                placeholder.classList.remove('hidden');
                container.innerHTML = '<span class="text-sm text-gray-400 italic" id="colorsPlaceholder">No colors added yet</span>';
            } else {
                container.innerHTML = colors.map(color => `
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium text-white gap-2"
                          style="background-color: #800000;">
                        ${color}
                        <button type="button" onclick="removeColor('${color}')" 
                                class="hover:bg-red-800 rounded-full w-4 h-4 flex items-center justify-center">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>
                `).join('');
            }
            
            hiddenInput.value = JSON.stringify(colors);
            
            // Update color dropdowns in image slots
            updateImageColorDropdowns();
        }
        
        function updateImageColorDropdowns() {
            const selects = document.querySelectorAll('select[name="image_colors[]"]');
            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">No color</option>' + 
                    colors.map(color => `<option value="${color}" ${currentValue === color ? 'selected' : ''}>${color}</option>`).join('');
            });
        }
        </script>

        <!-- Description -->
        <div>
            <label class="block font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4"
                class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-600">{{ old('description') }}</textarea>
        </div>

        <!-- Professional Image Upload Section -->
        <div class="border-2 border-dashed rounded-lg p-6" style="border-color: #800000;">
            <label class="block font-bold text-gray-900 mb-4 text-lg">
                <i class="fas fa-images mr-2" style="color: #800000;"></i>Product Images
            </label>
            <p class="text-sm text-gray-600 mb-4">
                Upload up to 9 images. The first image will be the main product image. Recommended size: 800x800px.
            </p>
            
            <!-- Image Upload Area -->
            <div id="imageUploadArea" class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-4">
                <!-- Main Image Slot -->
                <div class="image-slot relative aspect-square border-2 rounded-lg overflow-hidden bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer group"
                     style="border-color: #800000;" onclick="document.getElementById('mainImageInput').click()">
                    <input type="file" id="mainImageInput" name="images[]" accept="image/*" class="hidden" onchange="handleImageSelect(event, 0)">
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <i class="fas fa-camera text-3xl mb-2" style="color: #800000;"></i>
                        <span class="text-xs font-bold" style="color: #800000;">Main Image</span>
                        <span class="text-xs text-gray-500">Required</span>
                    </div>
                    <div class="preview-container hidden absolute inset-0">
                        <img src="" alt="" class="w-full h-full object-cover">
                        <div class="absolute top-1 right-1">
                            <button type="button" onclick="removeImage(event, 0)" 
                                    class="bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                            <span class="text-white text-xs font-bold">
                                <i class="fas fa-star"></i> Main
                            </span>
                            <select name="image_colors[]" class="w-full mt-1 text-xs bg-white/90 rounded px-1 py-0.5" onchange="updateImageColor(0, this.value)">
                                <option value="">No color</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Image Slots (8 more) -->
                @for ($i = 1; $i < 9; $i++)
                <div class="image-slot relative aspect-square border-2 border-dashed rounded-lg overflow-hidden bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer group"
                     onclick="document.getElementById('imageInput{{ $i }}').click()">
                    <input type="file" id="imageInput{{ $i }}" name="images[]" accept="image/*" class="hidden" onchange="handleImageSelect(event, {{ $i }})">
                    <div class="absolute inset-0 flex flex-col items-center justify-center opacity-50 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-plus text-2xl text-gray-400"></i>
                        <span class="text-xs text-gray-400 mt-1">Add</span>
                    </div>
                    <div class="preview-container hidden absolute inset-0">
                        <img src="" alt="" class="w-full h-full object-cover">
                        <div class="absolute top-1 right-1">
                            <button type="button" onclick="removeImage(event, {{ $i }})" 
                                    class="bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 shadow-lg">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-black/70 p-1">
                            <select name="image_colors[]" class="w-full text-xs bg-white/90 rounded px-1 py-0.5" onchange="updateImageColor({{ $i }}, this.value)">
                                <option value="">No color</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Image Guidelines -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Image Guidelines:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Format: JPG, PNG, WEBP</li>
                            <li>Size: Maximum 5MB per image</li>
                            <li>Recommended: Square images (1:1 ratio) at least 800x800px</li>
                            <li>First image will be displayed as main product image</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let uploadedImages = [];
        let imageColors = {}; // Track color for each image index
        
        function updateImageColor(index, color) {
            imageColors[index] = color;
            console.log('Image', index, 'assigned color:', color);
        }
        
        function handleImageSelect(event, index) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                event.target.value = '';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size must be less than 5MB');
                event.target.value = '';
                return;
            }
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const slots = document.querySelectorAll('.image-slot');
                const slot = slots[index];
                const preview = slot.querySelector('.preview-container');
                const img = preview.querySelector('img');
                const emptyState = slot.querySelector('.absolute.inset-0.flex');
                
                img.src = e.target.result;
                preview.classList.remove('hidden');
                emptyState.classList.add('hidden');
                
                uploadedImages[index] = file;
            };
            reader.readAsDataURL(file);
        }
        
        function removeImage(event, index) {
            event.stopPropagation();
            event.preventDefault();
            
            const slots = document.querySelectorAll('.image-slot');
            const slot = slots[index];
            const preview = slot.querySelector('.preview-container');
            const img = preview.querySelector('img');
            const emptyState = slot.querySelector('.absolute.inset-0.flex');
            const input = document.getElementById(index === 0 ? 'mainImageInput' : `imageInput${index}`);
            
            img.src = '';
            preview.classList.add('hidden');
            emptyState.classList.remove('hidden');
            input.value = '';
            uploadedImages[index] = null;
        }
        </script>

        <!-- Status -->
        <div>
            <label class="block font-medium text-gray-700">Status</label>
            <select name="status"
                class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-600" required>
                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors duration-200">
            Create Product
        </button>
    </form>
</div>
@endsection
