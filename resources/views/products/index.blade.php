@extends('layouts.app')

@section('title', 'Shop - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <div   class="auto-style-0167">
        <h1   class="auto-style-0168">
         Collection
        </h1>
        <p   class="auto-style-0194">Discover our latest collection of luxury fashion.</p>
    </div>

    <!-- Filters -->
    <div   class="auto-style-0170">
        <div   class="auto-style-0195">
            <div>
                <label   class="auto-style-0196">Category:</label>
                <select onchange="window.location.href=this.value"   class="auto-style-0172">
                    <option value="{{ route('shop') }}">All Items</option>
                    @foreach(\App\Models\Category::where('is_active', true)->get() as $cat)
                        <option value="{{ route('shop') }}?category={{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label   class="auto-style-0196">Gender:</label>
                <select onchange="window.location.href=this.value"   class="auto-style-0172">
                    <option value="{{ route('shop') }}">All</option>
                    <option value="{{ route('shop') }}?gender=women" {{ request('gender') == 'women' ? 'selected' : '' }}>Women's</option>
                    <option value="{{ route('shop') }}?gender=men" {{ request('gender') == 'men' ? 'selected' : '' }}>Men's</option>
                    <option value="{{ route('shop') }}?gender=unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>
        </div>
        <div>
            <label   class="auto-style-0196">Sort By:</label>
            <select onchange="window.location.href=this.value"   class="auto-style-0172">
                <option value="{{ route('shop') }}?sort=featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                <option value="{{ route('shop') }}?sort=price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="{{ route('shop') }}?sort=price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="{{ route('shop') }}?sort=newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div   class="auto-style-0173">
        @forelse($products as $product)
            <div   
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.borderColor='rgba(212, 175, 55, 0.3)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(212, 175, 55, 0.1)'" class="auto-style-0197">
                
                <!-- Product Image -->
                <div class="auto-style-0198" style="position: relative;">
                    <!-- Wishlist Toggle Heart -->
                    <button class="wishlist-heart-btn" onclick="toggleWishlist(event, {{ $product->id }}, this)" aria-label="Toggle Wishlist" style="position: absolute; top: 1rem; left: 1rem; background: var(--zyra-white); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 10; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <svg class="heart-icon {{ \App\Models\Wishlist::isProductWishlisted($product->id) ? 'active' : '' }}" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="transition: all 0.3s ease;">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>

                    <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                    @if($product->is_featured)
                        <div class="auto-style-0199">
                            Featured
                        </div>
                    @endif
                    @if(isset($product->ai_recommendation))
                        <div   class="auto-style-0200">
                            AI
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div   class="auto-style-0177">
                    <div   class="auto-style-0201">
                        {{ $product->category->name }}
                    </div>
                    <h3   class="auto-style-0178">
                        {{ $product->name }}
                    </h3>
                    <div class="auto-style-0179">
                        @if($product->is_effectively_on_sale)
                            <span style="color: var(--zyra-rose); font-weight: 700;">&#8377;{{ number_format($product->active_price, 2) }}</span>
                            <span style="font-size: 0.8rem; color: var(--zyra-silver); text-decoration: line-through; margin-left: 0.5rem;">&#8377;{{ number_format($product->price, 2) }}</span>
                        @else
                            &#8377;{{ number_format($product->active_price, 2) }}
                        @endif
                        <div style="font-size: 0.7rem; color: var(--zyra-silver); margin-top: 0.25rem;">* Price excludes taxes</div>
                    </div>

                    @if(isset($product->ai_recommendation))
                        <div   class="auto-style-0202">
                            ?? AI suggests: <strong   class="auto-style-0134">Size {{ $product->ai_recommendation['recommended_size'] }}</strong> ({{ $product->ai_recommendation['confidence'] }}% confidence)
                        </div>
                    @endif

                    <a href="{{ route('products.show', $product->slug) }}" style="display: block; width: 100%; padding: 0.9rem; background: linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose)); border: none; color: var(--zyra-black); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.85rem; text-align: center; text-decoration: none; transition: all 0.3s ease;">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div   class="auto-style-0181">
                <p   class="auto-style-0182">No products found matching your criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div   class="auto-style-0183">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

