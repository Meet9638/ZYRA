@extends('layouts.admin')

@section('page-title', 'Edit Product')

@push('styles')
<style>
    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.1);
        transition: 0.4s;
        border-radius: 34px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: var(--zyra-gold);
        border-color: var(--zyra-gold);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }

    .toggle-label {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 500;
    }

    .toggle-status {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .toggle-status.active {
        background: rgba(74, 222, 128, 0.2);
        color: var(--success);
    }

    .toggle-status.inactive {
        background: rgba(248, 113, 113, 0.2);
        color: var(--danger);
    }

    /* Form Styles */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        font-weight: 600;
        color: var(--zyra-white);
        font-size: 0.9rem;
    }

    .form-input, .form-select, .form-textarea {
        padding: 0.75rem;
        background: var(--zyra-charcoal);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        color: var(--zyra-white);
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--zyra-gold);
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* Image Upload Styles */
    .image-upload-area {
        border: 2px dashed rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: var(--zyra-charcoal);
    }

    .image-upload-area:hover {
        border-color: var(--zyra-gold);
        background: rgba(212, 175, 55, 0.05);
    }

    .existing-images {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .existing-image {
        position: relative;
        border-radius: 6px;
        overflow: hidden;
        background: var(--zyra-black);
    }

    .existing-image img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .remove-image {
        position: absolute;
        top: 4px;
        right: 4px;
        background: var(--danger);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-group.full-width {
            grid-column: span 1;
        }
    }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1>Edit Product</h1>
    <div class="admin-actions">
        <a href="{{ route('admin.products.index') }}" class="btn-secondary">
            ← Back to Products
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <!-- Product Name -->
        <div class="form-group full-width">
            <label class="form-label" for="name">Product Name *</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
        </div>

        <!-- Slug -->
        <div class="form-group">
            <label class="form-label" for="slug">Slug *</label>
            <input type="text" id="slug" name="slug" class="form-input" value="{{ old('slug', $product->slug) }}" required>
        </div>

        <!-- SKU -->
        <div class="form-group">
            <label class="form-label" for="sku">SKU *</label>
            <input type="text" id="sku" name="sku" class="form-input" value="{{ old('sku', $product->sku) }}" required>
        </div>

        <!-- Category -->
        <div class="form-group">
            <label class="form-label" for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach(\App\Models\Category::all() as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Gender -->
        <div class="form-group">
            <label class="form-label" for="gender">Gender *</label>
            <select id="gender" name="gender" class="form-select" required>
                <option value="">Select Gender</option>
                <option value="men" {{ old('gender', $product->gender) == 'men' ? 'selected' : '' }}>Men</option>
                <option value="women" {{ old('gender', $product->gender) == 'women' ? 'selected' : '' }}>Women</option>
                <option value="unisex" {{ old('gender', $product->gender) == 'unisex' ? 'selected' : '' }}>Unisex</option>
            </select>
        </div>

        <!-- Price -->
        <div class="form-group">
            <label class="form-label" for="price">Price *</label>
            <input type="number" id="price" name="price" class="form-input" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
        </div>

        <!-- Sale Status -->
        <div class="form-group">
            <label class="form-label">
                <input type="checkbox" id="on_sale" name="on_sale" value="1" {{ old('on_sale', $product->on_sale) ? 'checked' : '' }}>
                Enable Sale
            </label>
        </div>

        <!-- Discount Price -->
        <div class="form-group">
            <label class="form-label" for="discount_price">Discount Price</label>
            <input type="number" id="discount_price" name="discount_price" class="form-input" value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0">
        </div>

        <!-- Stock Quantity -->
        <div class="form-group">
            <label class="form-label" for="stock_quantity">Stock Quantity *</label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-input" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
        </div>

        <!-- Status Toggles -->
        <div class="form-group">
            <label class="form-label">Status & Visibility</label>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- Active Status -->
                <div class="toggle-label">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>Active</span>
                    <span class="toggle-status {{ old('is_active', $product->is_active) ? 'active' : 'inactive' }}">
                        {{ old('is_active', $product->is_active) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Featured Status -->
                <div class="toggle-label">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>Featured</span>
                    <span class="toggle-status {{ old('is_featured', $product->is_featured) ? 'active' : 'inactive' }}">
                        {{ old('is_featured', $product->is_featured) ? 'Featured' : 'Not Featured' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Sizes Selection -->
        <div class="form-group full-width">
            <label class="form-label">Available Sizes *</label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; padding: 1rem; background: var(--zyra-charcoal); border-radius: 8px;">
                @php
                    $productSizeArr = $product->productSizes->pluck('size')->toArray();
                @endphp
                @foreach(['XS','S','M','L','XL','XXL','XXXL','28','30','32','34','36','38','40'] as $s)
                    <div style="display: flex; align-items: center; gap: 5px; min-width: 60px;">
                        <input type="checkbox" name="sizes[]" value="{{ $s }}" id="sz_{{ $s }}"
                            {{ in_array($s, old('sizes', $productSizeArr)) ? 'checked' : '' }}>
                        <label for="sz_{{ $s }}" style="cursor: pointer; font-size: 0.9rem;">{{ $s }}</label>
                    </div>
                @endforeach
            </div>
            @error('sizes')<small style="color: var(--danger);">{{ $message }}</small>@enderror
        </div>

        <!-- Description -->
        <div class="form-group full-width">
            <label class="form-label" for="description">Description *</label>
            <textarea id="description" name="description" class="form-textarea" required>{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Main Image -->
        <div class="form-group full-width">
            <label class="form-label" for="main_image">Main Image</label>
            @if($product->main_image)
                <div class="existing-images">
                    <div class="existing-image">
                        <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}">
                        <button type="button" class="remove-image" onclick="removeMainImage()">×</button>
                    </div>
                </div>
            @endif
            <input type="file" id="main_image" name="main_image" class="form-input" accept="image/*">
            <small style="color: var(--zyra-silver); font-size: 0.8rem;">Leave empty to keep current image</small>
        </div>

        <!-- Additional Images -->
        <div class="form-group full-width">
            <label class="form-label" for="additional_images">Additional Images</label>
            @if($product->additional_images && count(json_decode($product->additional_images)) > 0)
                <div class="existing-images">
                    @foreach(json_decode($product->additional_images) as $index => $image)
                        <div class="existing-image">
                            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }} - {{ $index + 2 }}">
                            <button type="button" class="remove-image" onclick="removeAdditionalImage('{{ $image }}')">×</button>
                        </div>
                    @endforeach
                </div>
            @endif
            <input type="file" id="additional_images" name="additional_images[]" class="form-input" accept="image/*" multiple>
            <small style="color: var(--zyra-silver); font-size: 0.8rem;">Select multiple images or leave empty to keep current images</small>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="admin-actions" style="margin-top: 2rem;">
        <button type="submit" class="btn-primary">
            Update Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn-secondary">
            Cancel
        </a>
    </div>
</form>

<script>
    // Toggle status text update
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const statusSpan = this.closest('.toggle-label').querySelector('.toggle-status');
            const isActive = this.checked;
            
            if (this.name === 'is_active') {
                statusSpan.textContent = isActive ? 'Active' : 'Inactive';
            } else if (this.name === 'is_featured') {
                statusSpan.textContent = isActive ? 'Featured' : 'Not Featured';
            }
            
            statusSpan.className = 'toggle-status ' + (isActive ? 'active' : 'inactive');
        });
    });

    // Price validation
    function validatePrices() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const discountPrice = parseFloat(document.getElementById('discount_price').value) || 0;
        const onSale = document.getElementById('on_sale').checked;
        
        if (onSale && discountPrice >= price && discountPrice > 0) {
            alert('Discount price must be less than the original price when sale is enabled.');
            document.getElementById('discount_price').focus();
            return false;
        }
        
        if (discountPrice < 0) {
            alert('Discount price cannot be negative.');
            document.getElementById('discount_price').focus();
            return false;
        }
        
        return true;
    }

    // Add event listeners for price validation
    document.getElementById('price')?.addEventListener('input', validatePrices);
    document.getElementById('discount_price')?.addEventListener('input', validatePrices);
    document.getElementById('on_sale')?.addEventListener('change', validatePrices);

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validatePrices()) {
            e.preventDefault();
        }
    });

    // Remove main image
    function removeMainImage() {
        const input = document.getElementById('main_image');
        input.value = '';
        input.style.borderColor = 'var(--zyra-gold)';
        
        // Add hidden field to indicate removal
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'remove_main_image';
        hiddenField.value = '1';
        input.parentNode.appendChild(hiddenField);
        
        // Remove the image preview
        event.target.closest('.existing-image').remove();
    }

    // Remove additional image
    function removeAdditionalImage(imagePath) {
        const container = document.querySelector('.existing-images');
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'remove_additional_images[]';
        hiddenField.value = imagePath;
        container.parentNode.appendChild(hiddenField);
        
        // Remove the image preview
        event.target.closest('.existing-image').remove();
    }

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
