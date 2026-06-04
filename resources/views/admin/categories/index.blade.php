@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
    <div class="admin-filters">
        <form method="GET" action="{{ route('admin.categories.index') }}" style="display: flex; gap: 1rem; flex: 1;">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="admin-input"
                placeholder="Search categories"
            >
            <select name="status" class="admin-select">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>

        <div style="display: flex; gap: 1rem;">
            <button type="button" class="btn-primary" style="background: var(--danger); border-color: var(--danger);" onclick="submitMassDelete('category')">
                🗑️ Delete Selected
            </button>
            <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add Category</a>
        </div>
    </div>

    <form id="massDeleteForm" method="POST" style="display: none;">
        @csrf
    </form>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)"></th>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" class="row-checkbox" value="{{ $category->id }}">
                        </td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count ?? 0 }}</td>
                        <td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($categories, 'links'))
        <div class="pagination">
            {{ $categories->links() }}
        </div>
    @endif

@push('scripts')
<script>
    function toggleAllCheckboxes(source) {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function submitMassDelete(modelType) {
        let selected = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => selected.push(cb.value));
        
        if (selected.length === 0) {
            alert('Please select at least one item to delete.');
            return;
        }
        
        if (confirm('Are you sure you want to permanently delete the ' + selected.length + ' selected item(s)?')) {
            let form = document.getElementById('massDeleteForm');
            form.action = '/admin/mass-destroy/' + modelType;
            
            selected.forEach(id => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            form.submit();
        }
    }
</script>
@endpush
@endsection
