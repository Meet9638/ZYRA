@extends('layouts.admin')

@section('title', 'Add Category')
@section('page-title', 'Add Category')

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data"   class="auto-style-0001">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea
                id="description"
                name="description"
                class="form-control"
            >{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="image">Image</label>
            <input
                id="image"
                type="file"
                name="image"
                class="form-control"
                accept="image/*"
            >
        </div>

        <div   class="auto-style-0002">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
