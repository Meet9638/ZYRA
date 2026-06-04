@extends('layouts.app')

@section('title', 'Your Atelier Wishlist - ZYRA')

@section('content')
<div class="container" style="padding-top: 4rem; min-height: 70vh;">
    <!-- Breadcrumbs -->
    <nav style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver);">
        <a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <span style="color: var(--zyra-gold);">Wishlist</span>
    </nav>

    <!-- Header Section -->
    <div style="margin-bottom: 4rem; text-align: center;">
        <span style="font-size: 0.85rem; font-weight: 700; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 1rem; display: block;">Your Selection</span>
        <h1 style="font-size: clamp(2.5rem, 5vw, 4rem); font-family: 'Syne', sans-serif; font-weight: 800; line-height: 1;" class="text-gradient">
            Atelier Wishlist
        </h1>
        <p style="color: var(--zyra-silver); font-size: 1.05rem; margin-top: 1rem; font-family: 'Inter', sans-serif;">
            Keep track of your favorite curated pieces and add them to your collection when ready.
        </p>
    </div>

    @if(count($items) > 0)
        <!-- Wishlist Items Grid -->
        <div class="wishlist-grid">
            @foreach($items as $product)
                <div class="wishlist-card glass-card">
                    <!-- Absolute Close Button -->
                    <button class="remove-btn" onclick="removeFromWishlist({{ $product->id }}, this)" aria-label="Remove item">
                        &times;
                    </button>

                    <!-- Product Image Wrapper -->
                    <div class="card-img-container">
                        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                        @if($product->is_featured)
                            <div class="badge-featured">Featured</div>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Category & Title -->
                        <span class="card-cat">{{ $product->category?->name ?? 'Premium Selection' }}</span>
                        <h3 class="card-title">{{ $product->name }}</h3>

                        <!-- Stock Availability -->
                        <div class="stock-status">
                            @if($product->stock_quantity > 0)
                                <span class="status-in">● In Atelier</span>
                            @else
                                <span class="status-out">● Out of Stock</span>
                            @endif
                        </div>

                        <!-- Pricing -->
                        <div class="card-price">
                            @if($product->is_effectively_on_sale)
                                <span class="price-active">&#8377;{{ number_format($product->active_price, 2) }}</span>
                                <span class="price-orig">&#8377;{{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="price-active">&#8377;{{ number_format($product->active_price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Product SKU reference -->
                        <div style="font-size: 0.75rem; color: var(--zyra-silver); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            SKU: {{ $product->sku }}
                        </div>

                        <!-- Size & Move to Cart actions -->
                        <div class="card-actions">
                            <div class="form-group" style="margin-bottom: 1.25rem;">
                                <label class="lbl" style="font-size: 0.7rem; margin-bottom: 0.5rem; display: block; text-transform: uppercase; font-weight: 700;">Select Size</label>
                                <select class="form-control size-selector" style="cursor: pointer; padding: 0.6rem 0.8rem; background: var(--zyra-gray); border-color: var(--border-color); color: var(--zyra-white); font-weight: 600;">
                                    <option value="" disabled selected>Select Size</option>
                                    @forelse($product->productSizes as $size)
                                        @if($size->stock_quantity > 0)
                                            <option value="{{ $size->size }}">{{ $size->size }} ({{ $size->stock_quantity }} available)</option>
                                        @else
                                            <option value="{{ $size->size }}" disabled>{{ $size->size }} (Out of Stock)</option>
                                        @endif
                                    @empty
                                        <option value="bespoke">Bespoke Drape</option>
                                    @endforelse
                                </select>
                            </div>

                            @if($product->stock_quantity > 0)
                                <button type="button" class="btn-premium card-add-btn" onclick="moveToCart({{ $product->id }}, this)">
                                    Move to Atelier Bag
                                </button>
                            @else
                                <button type="button" class="btn-premium card-add-btn" style="opacity: 0.5; cursor: not-allowed;" disabled>
                                    Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty Wishlist State -->
        <div class="empty-wishlist-state glass-card">
            <span style="font-size: 4rem; display: block; margin-bottom: 2rem; filter: drop-shadow(0 10px 20px rgba(232, 111, 28, 0.2)); animate: float 6s ease-in-out infinite;">🖤</span>
            <h2 style="font-family: 'Syne', sans-serif; font-size: 2.2rem; font-weight: 700; margin-bottom: 1rem; color: var(--zyra-white);">Your Wishlist is Empty</h2>
            <p style="color: var(--zyra-silver); max-width: 480px; margin: 0 auto 3rem auto; line-height: 1.8; font-size: 1rem;">
                Explore our signature collections and curate your perfect wardrobe with our premium hand-crafted silhouettes.
            </p>
            <a href="{{ route('shop') }}" class="btn-premium" style="display: inline-block;">
                Explore Collection
            </a>
        </div>
    @endif
</div>

<style>
    /* Premium Wishlist Styling */
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 3rem;
        margin-top: 2rem;
    }

    .wishlist-card {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .wishlist-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 45px rgba(232, 111, 28, 0.08) !important;
        border-color: rgba(232, 111, 28, 0.3) !important;
    }

    .remove-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.9);
        color: var(--zyra-white);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        cursor: pointer;
        z-index: 12;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        line-height: 1;
    }

    .remove-btn:hover {
        background: var(--danger) !important;
        color: white !important;
        transform: scale(1.15) rotate(90deg);
    }

    .card-img-container {
        aspect-ratio: 4/5;
        overflow: hidden;
        position: relative;
        background: var(--zyra-gray);
    }

    .card-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .wishlist-card:hover .card-img-container img {
        transform: scale(1.08);
    }

    .badge-featured {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        background: var(--zyra-white);
        color: var(--zyra-black);
        padding: 0.4rem 0.8rem;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        border-radius: 20px;
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .card-body {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-cat {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: var(--zyra-gold);
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: block;
    }

    .card-title {
        font-size: 1.4rem;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--zyra-white);
        line-height: 1.25;
    }

    .stock-status {
        margin-bottom: 1.25rem;
    }

    .status-in {
        color: var(--success);
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-out {
        color: var(--danger);
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .card-price {
        display: flex;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .price-active {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--zyra-white);
    }

    .price-orig {
        font-size: 0.95rem;
        color: var(--zyra-silver);
        text-decoration: line-through;
    }

    .card-actions {
        margin-top: auto;
    }

    .card-add-btn {
        width: 100%;
        text-align: center;
        padding: 0.9rem;
        font-size: 0.85rem;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .empty-wishlist-state {
        padding: 6rem 3rem;
        text-align: center;
        max-width: 720px;
        margin: 4rem auto 0 auto;
    }

    /* Responsive Queries */
    @media (max-width: 1100px) {
        .wishlist-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }
    }

    @media (max-width: 700px) {
        .wishlist-grid {
            grid-template-columns: 1fr;
            gap: 2.5rem;
            max-width: 450px;
            margin: 2rem auto 0 auto;
        }
        
        .empty-wishlist-state {
            padding: 4rem 1.5rem;
        }
    }
</style>

<script>
    // Remove item from wishlist via AJAX
    function removeFromWishlist(productId, btnElement) {
        const card = btnElement.closest('.wishlist-card');
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';

        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                const data = res.body;
                
                // Beautiful fade-out/slide-down transitions
                card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.remove();
                    // Update header counters
                    if (document.getElementById('wishlistCount')) {
                        document.getElementById('wishlistCount').textContent = data.wishlist_count;
                    }
                    // Refresh view if zero items remaining to show empty state template
                    const remaining = document.querySelectorAll('.wishlist-card');
                    if (remaining.length === 0) {
                        window.location.reload();
                    }
                }, 500);

                showNotification(data.message, 'success');
            } else {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
                showNotification(res.body.message || 'An error occurred', 'error');
            }
        })
        .catch(err => {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
            showNotification('Something went wrong. Please try again.', 'error');
        });
    }

    // Move item from wishlist to active shopping cart bag
    function moveToCart(productId, btnElement) {
        const card = btnElement.closest('.wishlist-card');
        const sizeSelect = card.querySelector('.size-selector');
        const size = sizeSelect.value;

        if (!size) {
            showNotification('Please select a size first.', 'error');
            
            // Add momentary attention shake/border-glow
            sizeSelect.style.borderColor = 'var(--zyra-gold)';
            sizeSelect.focus();
            setTimeout(() => sizeSelect.style.borderColor = 'var(--border-color)', 2000);
            return;
        }

        btnElement.disabled = true;
        btnElement.innerText = 'Moving to Bag...';

        fetch(`/wishlist/${productId}/move-to-cart`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ size: size, quantity: 1 })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                const data = res.body;
                
                // Beautiful fade-out/slide-down transitions
                card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.remove();
                    // Update header counters
                    if (document.getElementById('wishlistCount')) {
                        document.getElementById('wishlistCount').textContent = data.wishlist_count;
                    }
                    if (document.getElementById('cartCount')) {
                        document.getElementById('cartCount').textContent = data.cart_count;
                    }
                    
                    // Refresh view if zero items remaining
                    const remaining = document.querySelectorAll('.wishlist-card');
                    if (remaining.length === 0) {
                        window.location.reload();
                    }
                }, 500);

                showNotification(data.message, 'success');
            } else if (res.status === 401) {
                window.location.href = res.body.redirect || '/login';
            } else {
                btnElement.disabled = false;
                btnElement.innerText = 'Move to Atelier Bag';
                showNotification(res.body.message || 'An error occurred', 'error');
            }
        })
        .catch(err => {
            btnElement.disabled = false;
            btnElement.innerText = 'Move to Atelier Bag';
            showNotification('Something went wrong. Please try again.', 'error');
        });
    }
</script>
@endsection
