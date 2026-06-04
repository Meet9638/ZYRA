@extends('layouts.app')

@section('title', $category->name . ' - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <div   class="auto-style-0167">
        <h1   class="auto-style-0168">
            {{ $category->name }}
        </h1>
        @if($category->description)
        <p   class="auto-style-0194">{{ $category->description }}</p>
        @endif
    </div>

    <!-- Products Grid -->
    <div   class="auto-style-0173">
        @forelse($products as $product)
            <div   
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.borderColor='rgba(212, 175, 55, 0.3)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(212, 175, 55, 0.1)'" class="auto-style-0197">
                
                <!-- Product Image -->
                <div   class="auto-style-0198">
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--zyra-gray); color: var(--zyra-silver);">
                            No Image
                        </div>
                    @endif
                    @if($product->is_featured)
                        <div   class="auto-style-0199">
                            Featured
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
                        @if($product->discount_price)
                            <span style="color: var(--zyra-rose); font-weight: 700;">₹{{ number_format($product->discount_price, 2) }}</span>
                            <span style="font-size: 0.8rem; color: var(--zyra-silver); text-decoration: line-through; margin-left: 0.5rem;">₹{{ number_format($product->price, 2) }}</span>
                        @else
                            ₹{{ number_format($product->price, 2) }}
                        @endif
                    </div>

                    <a href="{{ route('products.show', $product->slug) }}" style="display: block; width: 100%; padding: 0.9rem; background: linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose)); border: none; color: var(--zyra-black); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.85rem; text-align: center; text-decoration: none; transition: all 0.3s ease; margin-top: 1rem;">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div   class="auto-style-0181">
                <p   class="auto-style-0182">No products found in this category.</p>
                <a href="{{ route('shop') }}" style="display: inline-block; padding: 0.9rem 2rem; background: linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose)); border: none; color: var(--zyra-black); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.85rem; text-decoration: none; transition: all 0.3s ease; margin-top: 1.5rem;">
                    Continue Shopping
                </a>
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

