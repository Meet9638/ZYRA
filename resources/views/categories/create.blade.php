@extends('layouts.app')

@section('title', $category->name . ' - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <!-- Category Header -->
    <div   class="auto-style-0167">
        <h1   class="auto-style-0168">
            {{ $category->name }}
        </h1>
        @if($category->description)
            <p   class="auto-style-0169">
                {{ $category->description }}
            </p>
        @endif
    </div>

    <!-- Filters -->
    <div   class="auto-style-0170">
        <div   class="auto-style-0171">
            <select onchange="window.location.href=this.value"   class="auto-style-0172">
                <option value="{{ route('categories.show', $category->slug) }}">All</option>
                <option value="{{ route('categories.show', $category->slug) }}?gender=women" {{ request('gender') == 'women' ? 'selected' : '' }}>Women's</option>
                <option value="{{ route('categories.show', $category->slug) }}?gender=men" {{ request('gender') == 'men' ? 'selected' : '' }}>Men's</option>
                <option value="{{ route('categories.show', $category->slug) }}?gender=unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
            </select>
        </div>
        <div>
            <select onchange="window.location.href=this.value"   class="auto-style-0172">
                <option value="{{ route('categories.show', $category->slug) }}">Sort By</option>
                <option value="{{ route('categories.show', $category->slug) }}?sort=price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="{{ route('categories.show', $category->slug) }}?sort=price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="{{ route('categories.show', $category->slug) }}?sort=newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div   class="auto-style-0173">
        @forelse($products as $product)
            <div   
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.borderColor='rgba(212, 175, 55, 0.3)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(212, 175, 55, 0.1)'" class="auto-style-0174">
                
                <div   class="auto-style-0175">
                    <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @if($product->is_featured)
                        <div   class="auto-style-0176">
                            Featured
                        </div>
                    @endif
                </div>

                <div   class="auto-style-0177">
                    <h3   class="auto-style-0178">
                        {{ $product->name }}
                    </h3>
                    <div   class="auto-style-0179">
                        &#8377;{{ number_format($product->price, 2) }}
                    </div>

                    <a href="{{ route('products.show', $product->slug) }}" style="display: block; width: 100%; padding: 0.9rem; background: linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose)); color: var(--zyra-black); font-weight: 600; text-transform: uppercase; text-align: center; text-decoration: none;">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div   class="auto-style-0181">
                <p   class="auto-style-0182">No products found in this category.</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div   class="auto-style-0183">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

