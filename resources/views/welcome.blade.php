@extends('layouts.app')

@section('title', 'ZYRA - Premium Fashion Experience')

@section('content')
<!-- Hero Section -->
<section class="hero" style="min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden; background: var(--zyra-black);">
    <div class="hero-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.15; z-index: 1;">
        <svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="var(--zyra-gold)" stroke-width="0.1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
    
    <div class="container" style="position: relative; z-index: 2; width: 100%;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div class="hero-content">
                <span style="font-size: 0.9rem; font-weight: 700; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 2rem; display: block;">Established 2026</span>
                <h1 class="hero-title" style="font-size: clamp(3rem, 8vw, 5.5rem); line-height: 0.95; margin-bottom: 2rem; font-family: 'Syne', sans-serif;">
                    Your Style,<br>
                    <span class="text-gradient">Exquisitely</span><br>
                    Crafted
                </h1>
                <p class="hero-subtitle" style="font-size: 1.2rem; color: var(--zyra-silver); max-width: 500px; margin-bottom: 3rem; line-height: 1.8;">
                    Discover the apex of luxury fashion. Where every stitch tells a story of curated excellence and timeless elegance.
                </p>
                <div class="hero-buttons" style="display: flex; gap: 2rem;">
                    <a href="{{ route('shop') }}" class="btn-premium">Explore Collection</a>
                    <a href="{{ route('about') }}" style="display: flex; align-items: center; gap: 0.8rem; text-decoration: none; color: var(--zyra-white); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; transition: gap 0.3s ease;" onmouseover="this.style.gap='1.5rem'" onmouseout="this.style.gap='0.8rem'">
                        Our Story <span>→</span>
                    </a>
                </div>
            </div>
            <div style="position: relative; height: 600px; display: flex; justify-content: center; align-items: center;">
                <!-- Floating Logo Symbol -->
                <div class="animate-float" style="width: 400px; height: 400px; position: relative;">
                    <img src="{{ asset('images/logo.png') }}" alt="ZYRA Symbol" style="width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 20px 50px rgba(232, 111, 28, 0.2));">
                </div>
                <!-- Glassmorphism Stats Overlay -->
                <div class="glass-card" style="position: absolute; bottom: 0; left: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1rem; transform: translateX(-10%);">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: var(--zyra-gold);">100%</span>
                        <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">Hand-picked<br>Curation</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Marquee Branding -->
<div class="marquee-container">
    <div class="marquee-content">
        <span class="marquee-item">Luxury</span>
        <span class="marquee-item">Excellence</span>
        <span class="marquee-item">ZYRA</span>
        <span class="marquee-item">Craftsmanship</span>
        <span class="marquee-item">Modernity</span>
        <span class="marquee-item">Luxury</span>
        <span class="marquee-item">Excellence</span>
        <span class="marquee-item">ZYRA</span>
        <span class="marquee-item">Craftsmanship</span>
        <span class="marquee-item">Modernity</span>
    </div>
</div>

<!-- Featured Products Section -->
<section style="padding: 10rem 0; background: var(--zyra-charcoal);">
    <div class="container">
        <div style="margin-bottom: 6rem; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 1rem; display: block;">Selection</span>
                <h2 style="font-size: 3.5rem; line-height: 1; font-family: 'Syne', sans-serif;">The Curated<br><span class="text-gradient">Edit</span></h2>
            </div>
            <a href="{{ route('shop') }}" style="text-decoration: none; color: var(--zyra-gold); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; border-bottom: 2px solid var(--zyra-gold); padding-bottom: 0.5rem;">View All Collection</a>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 3rem;">
            @forelse($products as $product)
            <a href="{{ route('products.show', $product->slug) }}" class="product-card-premium" style="text-decoration: none; color: inherit; display: block;">
                <div style="aspect-ratio: 4/5; background: var(--zyra-gray); position: relative; overflow: hidden; border-radius: 4px; margin-bottom: 2rem;">
                    <!-- Wishlist Toggle Heart -->
                    <button class="wishlist-heart-btn" onclick="toggleWishlist(event, {{ $product->id }}, this)" aria-label="Toggle Wishlist" style="position: absolute; top: 1.5rem; left: 1.5rem; background: var(--zyra-white); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 10; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <svg class="heart-icon {{ \App\Models\Wishlist::isProductWishlisted($product->id) ? 'active' : '' }}" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="transition: all 0.3s ease;">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>

                    @if($product->main_image)
                        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; opacity: 0.1;">💎</div>
                    @endif
                    <div style="position: absolute; top: 1.5rem; right: 1.5rem;">
                        <span style="background: var(--zyra-white); color: var(--zyra-black); padding: 0.5rem 1rem; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; border-radius: 20px;">
                            {{ $product->is_featured ? 'Featured' : 'Limited' }}
                        </span>
                    </div>
                </div>
                <div style="padding: 0 0.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <h3 style="font-size: 1.25rem; font-family: 'Syne', sans-serif;">{{ $product->name }}</h3>
                        <div style="font-weight: 700; color: var(--zyra-gold);">
                            @if($product->is_effectively_on_sale)
                                <span>&#8377;{{ number_format($product->active_price, 2) }}</span>
                                <span style="font-size: 0.8rem; color: var(--zyra-silver); text-decoration: line-through; margin-left: 0.5rem;">&#8377;{{ number_format($product->price, 2) }}</span>
                            @else
                                &#8377;{{ number_format($product->active_price, 2) }}
                            @endif
                        </div>
                    </div>
                    <p style="color: var(--zyra-silver); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em;">{{ $product->category?->name ?? 'Premium Selection' }}</p>
                    <p style="color: var(--zyra-silver); font-size: 0.7rem; margin-top: 0.25rem;">* Price excludes taxes</p>
                </div>
            </a>
            @empty
                <div style="grid-column: span 3; text-align: center; padding: 4rem; color: var(--zyra-silver);">
                    <p>No products available at the moment. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Values Section -->
<section style="padding: 10rem 0; background: var(--zyra-black);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4rem;">
            <div class="glass-card" style="padding: 3rem; text-align: left;">
                <span style="font-size: 2.5rem; margin-bottom: 2rem; display: block;">✨</span>
                <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem; font-family: 'Syne', sans-serif;">Artisanal Quality</h3>
                <p style="color: var(--zyra-silver); line-height: 1.8;">Meticulously crafted by master artisans using centuries-old techniques blended with modern precision.</p>
            </div>
            <div class="glass-card" style="padding: 3rem; text-align: left; transform: translateY(40px);">
                <span style="font-size: 2.5rem; margin-bottom: 2rem; display: block;">🏛️</span>
                <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem; font-family: 'Syne', sans-serif;">Ethical Luxury</h3>
                <p style="color: var(--zyra-silver); line-height: 1.8;">Responsible sourcing and fair labor practices are the bedrock of our commitment to true luxury.</p>
            </div>
            <div class="glass-card" style="padding: 3rem; text-align: left;">
                <span style="font-size: 2.5rem; margin-bottom: 2rem; display: block;">💎</span>
                <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem; font-family: 'Syne', sans-serif;">Global Exclusivity</h3>
                <p style="color: var(--zyra-silver); line-height: 1.8;">Access a worldwide network of limited-run pieces and exclusive designer collaborations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section style="padding: 12rem 0; background: linear-gradient(rgba(10,10,10,0.4), rgba(10,10,10,0.4)), url('https://images.unsplash.com/photo-1441996645815-23c288f00122?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=70') no-repeat center center / cover; position: relative;">
    <div class="container" style="text-align: center; color: var(--zyra-charcoal); position: relative; z-index: 2;">
        <h2 style="font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1; font-family: 'Syne', sans-serif; margin-bottom: 2.5rem;">Ready to redefine<br>your <span style="font-style: italic;">signature</span> style?</h2>
        <div style="display: flex; gap: 2rem; justify-content: center;">
            <a href="{{ route('register') }}" class="btn-premium" style="background: var(--zyra-gold); color: white;">Join The Elite</a>
            <a href="{{ route('shop') }}" class="btn-premium">Browse Shop</a>
        </div>
    </div>
</section>

<style>
    .product-card-premium:hover img {
        transform: scale(1.1);
    }
    .hero {
        perspective: 1000px;
    }
</style>
@endsection
