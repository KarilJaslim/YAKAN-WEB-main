@extends('layouts.admin')

@section('title', 'Cultural Heritage Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Cultural Heritage Management</h1>
                <p class="text-amber-100 text-lg">Manage Yakan history, traditions, and cultural content</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('admin.cultural-heritage.create') }}" class="bg-white text-amber-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New Content
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Content</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $heritages->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-amber-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Published</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $heritages->where('is_published', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Drafts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $heritages->where('is_published', false)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-gray-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Categories</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $heritages->pluck('category')->unique()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <form method="GET" class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search content..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Categories</option>
                    <option value="history" {{ request('category') == 'history' ? 'selected' : '' }}>History</option>
                    <option value="tradition" {{ request('category') == 'tradition' ? 'selected' : '' }}>Tradition</option>
                    <option value="culture" {{ request('category') == 'culture' ? 'selected' : '' }}>Culture</option>
                    <option value="art" {{ request('category') == 'art' ? 'selected' : '' }}>Art</option>
                    <option value="crafts" {{ request('category') == 'crafts' ? 'selected' : '' }}>Crafts</option>
                    <option value="language" {{ request('category') == 'language' ? 'selected' : '' }}>Language</option>
                </select>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.cultural-heritage.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Content List -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($heritages as $heritage)
                    <tr class="hover:bg-gray-50 transition-colors" data-heritage-id="{{ $heritage->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($heritage->image)
                                <img src="{{ $heritage->image_url }}" 
                                     alt="{{ $heritage->title }}" 
                                     class="w-16 h-16 rounded-lg object-cover mr-4"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4\'><i class=\'fas fa-image text-gray-400\'></i></div><div><div class=\'text-sm font-medium text-gray-900\'>{{ $heritage->title }}</div><div class=\'text-sm text-gray-500\'>{{ Str::limit($heritage->summary ?? $heritage->excerpt, 60) }}</div></div>';">
                                @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $heritage->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($heritage->summary ?? $heritage->excerpt, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                {{ ucfirst($heritage->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleStatus({{ $heritage->id }})" class="px-2 py-1 text-xs font-semibold rounded-full {{ $heritage->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $heritage->is_published ? 'Published' : 'Draft' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $heritage->order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $heritage->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.cultural-heritage.edit', $heritage->id) }}" class="text-amber-600 hover:text-amber-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete({{ $heritage->id }}, '{{ addslashes($heritage->title) }}')" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No cultural heritage content found</p>
                            <a href="{{ route('admin.cultural-heritage.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Your First Content
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($heritages->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $heritages->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Content</h3>
            <p class="text-gray-600 text-center mb-6">
                Are you sure you want to delete <strong id="heritageName" class="text-gray-900"></strong>? This action cannot be undone.
            </p>
            <div class="flex space-x-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button onclick="deleteHeritage()" id="confirmDeleteBtn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-4 right-4 z-50 transform transition-all duration-300">
    <div class="bg-white rounded-lg shadow-xl border-l-4 p-4 max-w-md">
        <div class="flex items-center">
            <div id="toastIcon" class="flex-shrink-0"></div>
            <div class="ml-3">
                <p id="toastMessage" class="text-sm font-medium"></p>
            </div>
            <button onclick="closeToast()" class="ml-auto flex-shrink-0">
                <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let heritageToDelete = null;

function confirmDelete(heritageId, heritageName) {
    heritageToDelete = heritageId;
    document.getElementById('heritageName').textContent = heritageName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    heritageToDelete = null;
}

function deleteHeritage() {
    if (!heritageToDelete) return;
    
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;
    
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
    
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/cultural-heritage/${heritageToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        closeDeleteModal();
        
        if (data.success) {
            showToast(data.message, 'success');
            
            const row = document.querySelector(`[data-heritage-id="${heritageToDelete}"]`);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    setTimeout(() => window.location.reload(), 1000);
                }, 300);
            } else {
                setTimeout(() => window.location.reload(), 1500);
            }
        } else {
            showToast(data.message || 'Failed to delete content', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeDeleteModal();
        showToast('An error occurred while deleting', 'error');
    })
    .finally(() => {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalText;
    });
}

function toggleStatus(heritageId) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/cultural-heritage/${heritageId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update status', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    const toastContainer = toast.querySelector('div');
    
    toastMessage.textContent = message;
    
    if (type === 'success') {
        toastIcon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
        toastContainer.classList.remove('border-red-500');
        toastContainer.classList.add('border-green-500');
    } else {
        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>';
        toastContainer.classList.remove('border-green-500');
        toastContainer.classList.add('border-red-500');
    }
    
    toast.classList.remove('hidden');
    setTimeout(() => closeToast(), 3000);
}

function closeToast() {
    document.getElementById('toast').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});

document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
