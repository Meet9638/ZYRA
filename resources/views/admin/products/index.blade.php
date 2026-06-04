@extends('layouts.admin')

@section('page-title', 'Products Management')

@push('styles')
<style>
    /* Product image thumbnail */
    .product-thumb {
        position: relative;
        width: 80px;
        height: 100px;
        background: #F8F8F8;
        border: 1px solid #EEEEEE;
        border-radius: 4px;
        overflow: visible;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
        display: block;
    }

    .product-thumb:hover {
        border-color: var(--zyra-gold);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* No image placeholder */
    .product-thumb .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #CCCCCC;
        font-size: 1.4rem;
        border-radius: 4px;
        background: #F8F8F8;
    }

    .product-thumb .no-image span {
        font-size: 0.6rem;
        margin-top: 3px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #999999;
    }

    /* Hover zoom preview - Premium Light */
    .product-thumb .img-preview {
        display: none;
        position: absolute;
        top: 0;
        left: calc(100% + 20px);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid #EEEEEE;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        width: 240px;
        opacity: 0;
        transform: scale(0.9) translateX(-10px);
        transition: all 0.3s ease;
    }

    .product-thumb:hover .img-preview {
        display: block;
        opacity: 1;
        transform: scale(1) translateX(0);
    }

    .img-preview img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    .img-preview .img-preview-name {
        margin-top: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #111;
        text-align: center;
    }

    /* Flip preview to left when near right edge */
    .product-thumb.flip-left .img-preview {
        left: auto;
        right: calc(100% + 20px);
        transform: scale(0.9) translateX(10px);
    }

    .product-thumb.flip-left:hover .img-preview {
        transform: scale(1) translateX(0);
    }

    /* Gallery strip (for multiple images) */
    .img-strip {
        display: flex;
        gap: 3px;
        margin-top: 5px;
    }

    .img-strip img {
        width: 20px;
        height: 20px;
        object-fit: cover;
        border-radius: 3px;
        opacity: 0.7;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .img-strip .more-imgs {
        width: 20px;
        height: 20px;
        background: rgba(212,175,55,0.15);
        border-radius: 3px;
        font-size: 0.55rem;
        color: var(--zyra-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<!-- Header Actions -->
<div class="admin-top-bar" style="margin-bottom: 2rem;">
    <div style="flex: 1;">
        <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; max-width: 400px;">
            <input 
                type="text" 
                name="search" 
                placeholder="Search products..." 
                value="{{ request('search') }}"
                class="admin-input"
            >
        </form>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button type="button" class="btn-primary" style="background: var(--danger); border-color: var(--danger);" onclick="submitMassDelete('product')">
            🗑️ Delete Selected
        </button>
        <a href="{{ route('admin.products.create') }}" class="btn-primary">
            + Add New Product
        </a>
    </div>
</div>

<form id="massDeleteForm" method="POST" style="display: none;">
    @csrf
</form>

<!-- Filters -->
<div class="admin-filters">
    <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <select name="category" onchange="this.form.submit()" class="admin-select">
            <option value="">All Categories</option>
            @foreach(\App\Models\Category::all() as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <select name="status" onchange="this.form.submit()" class="admin-select">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <select name="stock" onchange="this.form.submit()" class="admin-select">
            <option value="">All Stock</option>
            <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
            <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
            <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
    </form>
</div>

<!-- Products Table -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)"></th>
                <th>Image</th>
                <th>Product</th>
                <th>Category</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                @php
                    // Resolve all images: support both a `images` relation/array and a single `main_image`
                    $allImages = [];
                    if ($product->relationLoaded('images') && $product->images->count()) {
                        $allImages = $product->images->pluck('path')->toArray();
                    } elseif (!empty($product->gallery_images)) {
                        // gallery_images may be JSON-cast array of paths
                        $allImages = is_array($product->gallery_images)
                            ? $product->gallery_images
                            : json_decode($product->gallery_images, true) ?? [];
                    }

                    $mainImage  = $product->main_image ?? ($allImages[0] ?? null);
                    $extraImages = array_filter($allImages, fn($img) => $img !== $mainImage);
                    $totalImages = ($mainImage ? 1 : 0) + count($extraImages);
                @endphp
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="row-checkbox" value="{{ $product->id }}">
                    </td>
                    <!-- ── IMAGE CELL ── -->
                    <td   class="auto-style-0079">
                        <div class="product-thumb" id="thumb-{{ $product->id }}">

                            @if($mainImage)
                                {{-- Main thumbnail --}}
                                <img
                                    src="{{ asset('storage/' . $mainImage) }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    onerror="this.closest('.product-thumb').querySelector('.no-image') && (this.style.display='none'); this.nextElementSibling && (this.nextElementSibling.style.display='flex');"
                                >

                                {{-- Hover zoom preview --}}
                                <div class="img-preview">
                                    <img src="{{ asset('storage/' . $mainImage) }}" alt="{{ $product->name }}">
                                    <div class="img-preview-name">{{ $product->name }}</div>

                                    {{-- Extra images strip inside preview --}}
                                    @if(count($extraImages))
                                        <div class="img-strip auto-style-0080"  >
                                            @foreach(array_slice(array_values($extraImages), 0, 5) as $extra)
                                                <img src="{{ asset('storage/' . $extra) }}" alt="image">
                                            @endforeach
                                            @if(count($extraImages) > 5)
                                                <div class="more-imgs">+{{ count($extraImages) - 5 }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Image count badge --}}
                                @if($totalImages > 1)
                                    <span class="img-count-badge">{{ $totalImages }} imgs</span>
                                @endif

                            @else
                                {{-- No image fallback --}}
                                <div class="no-image">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                    <span>No image</span>
                                </div>
                            @endif

                        </div>
                    </td>

                    <td>
                        <div   class="auto-style-0048">{{ $product->name }}</div>
                        <div   class="auto-style-0081">{{ ucfirst($product->gender) }}</div>
                        @if($product->is_featured)
                            <span   class="auto-style-0082">FEATURED</span>
                        @endif
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td>
                        <code   class="auto-style-0083">
                            {{ $product->sku }}
                        </code>
                    </td>
                    <td>
                        <div   class="auto-style-0084">
                            ₹{{ number_format($product->price, 2) }}
                        </div>
                        @if($product->discount_price)
                            <div   class="auto-style-0085">
                                ₹{{ number_format($product->discount_price, 2) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 600; color: {{ $product->stock_quantity > 10 ? 'var(--success)' : ($product->stock_quantity > 0 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $product->stock_quantity }}
                        </div>
                        @if($product->stock_quantity <= 0)
                            <div   class="auto-style-0086">Out of Stock</div>
                        @elseif($product->stock_quantity < 10)
                            <div   class="auto-style-0087">Low Stock</div>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.products.update', $product) }}" method="POST"   class="auto-style-0088">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_active" value="{{ $product->is_active ? 0 : 1 }}">
                            <button type="submit" style="padding: 0.4rem 0.8rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; cursor: pointer; border: none;
                                {{ $product->is_active ? 'background: rgba(74, 222, 128, 0.2); color: var(--success);' : 'background: rgba(248, 113, 113, 0.2); color: var(--danger);' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; align-items: center; justify-content: flex-start;">
                            <a href="{{ route('products.show', $product->slug) }}" target="_blank" style="padding: 0.45rem 0.8rem; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: var(--zyra-white); text-decoration: none; font-size: 0.8rem; font-weight: 500; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                                View
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" style="padding: 0.45rem 0.8rem; background: transparent; border: 1px solid var(--zyra-gold); color: var(--zyra-gold); text-decoration: none; font-size: 0.8rem; font-weight: 500; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background='rgba(212, 175, 55, 0.1)'" onmouseout="this.style.background='transparent'">
                                Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="margin: 0; padding: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0.45rem 0.8rem; background: transparent; border: 1px solid var(--danger); color: var(--danger); font-size: 0.8rem; font-weight: 500; border-radius: 4px; cursor: pointer; transition: all 0.2s; font-family: inherit; line-height: normal;" onmouseover="this.style.background='rgba(248, 113, 113, 0.1)'" onmouseout="this.style.background='transparent'">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9"   class="auto-style-0092">
                        No products found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($products->hasPages())
    <div class="pagination">
        {{ $products->links() }}
    </div>
@endif

<!-- Quick Stats -->
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-label">Total Products</div>
        <div class="admin-stat-value">
            {{ \App\Models\Product::count() }}
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-label">Active Products</div>
        <div class="admin-stat-value">
            {{ \App\Models\Product::where('is_active', true)->count() }}
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-label">Out of Stock</div>
        <div class="admin-stat-value">
            {{ \App\Models\Product::where('stock_quantity', 0)->count() }}
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-label">Total Value</div>
        <div class="admin-stat-value">
            ₹{{ number_format(\App\Models\Product::sum('price'), 0) }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Flip hover preview to the left if the thumbnail is in the right half of the screen
    document.querySelectorAll('.product-thumb').forEach(function(thumb) {
        thumb.addEventListener('mouseenter', function() {
            const rect = thumb.getBoundingClientRect();
            if (rect.right + 200 > window.innerWidth) {
                thumb.classList.add('flip-left');
            } else {
                thumb.classList.remove('flip-left');
            }
        });
    });

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
