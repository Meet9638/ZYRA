@extends('layouts.admin')

@section('content')
<h3>Edit Category</h3>

<form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Image</label>
        @if($category->image)
            <div class="mb-2">
                <img src="{{ Storage::url($category->image) }}" alt="Category Image" style="max-height:150px;">
            </div>
        @endif
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
            <label class="form-check-label">Active</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection
