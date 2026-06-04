@extends('layouts.app')

@section('title', $product->name . ' - ZYRA')

@section('content')
<div class="container" style="padding-top: 4rem;">
    <!-- Breadcrumbs -->
    <nav style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver);">
        <a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <a href="{{ route('shop') }}" style="color: inherit; text-decoration: none;">Shop</a>
        <span>/</span>
        <span style="color: var(--zyra-gold);">{{ $product->name }}</span>
    </nav>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: start;">
        <!-- Image Section with Slider -->
        <div style="position: sticky; top: 120px;">
            <!-- Main Image Carousel -->
            <div class="glass-card" style="aspect-ratio: 4/5; overflow: hidden; margin-bottom: 1.5rem; position: relative;">
                <!-- Carousel Container -->
                <div class="carousel-container" style="position: relative; width: 100%; height: 100%;">
                    <!-- Main Image Display -->
                    <div class="carousel-main" style="width: 100%; height: 100%; position: relative;">
                        @if(count($product->all_images) > 0)
                            @foreach($product->all_images as $index => $imageUrl)
                                <div class="carousel-slide {{ $index == 0 ? 'active' : '' }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: {{ $index == 0 ? '1' : '0' }}; transition: opacity 0.5s ease;">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }} - Image {{ $index + 1 }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-slide active" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                <img src="{{ asset('images/placeholder-product.svg') }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                    </div>
                    
                    <!-- Navigation Arrows -->
                    @if(count($product->all_images) > 1)
                        <button class="carousel-arrow carousel-prev" onclick="changeSlide(-1)" style="position: absolute; top: 50%; left: 1rem; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 10;">
                            ‹
                        </button>
                        <button class="carousel-arrow carousel-next" onclick="changeSlide(1)" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 10;">
                            ›
                        </button>
                    @endif
                    
                    <!-- Product Badge -->
                    <div style="position: absolute; top: 2rem; left: 2rem; z-index: 10;">
                        @if($product->is_featured)
                            <span style="background: var(--zyra-gold); color: white; padding: 0.5rem 1rem; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; border-radius: 4px;">Exclusive Selection</span>
                        @endif
                    </div>
                    
                    <!-- Image Counter -->
                    @if(count($product->all_images) > 1)
                        <div style="position: absolute; bottom: 1rem; right: 1rem; background: rgba(0,0,0,0.7); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; z-index: 10;">
                            <span id="imageCounter">1 / {{ count($product->all_images) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Thumbnail Gallery -->
            @if(count($product->all_images) > 1)
            <div class="thumbnail-gallery" style="display: grid; grid-template-columns: repeat({{ min(4, count($product->all_images)) }}, 1fr); gap: 0.75rem;">
                @foreach($product->all_images as $index => $imageUrl)
                    <div class="thumbnail-item {{ $index == 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})" style="aspect-ratio: 1/1; cursor: pointer; border: 2px solid {{ $index == 0 ? 'var(--zyra-gold)' : 'var(--border-color)' }}; border-radius: 4px; overflow: hidden; transition: all 0.3s ease; opacity: {{ $index == 0 ? '1' : '0.7' }};">
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }} - Thumbnail {{ $index + 1 }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Details Section -->
        <div class="product-details">
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 1rem; display: block;">{{ $product->category->name }}</span>
            <h1 style="font-size: 3.5rem; line-height: 1.1; font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;" class="text-gradient">{{ $product->name }}</h1>
            
            <div style="margin-bottom: 3rem;">
                @if($product->is_effectively_on_sale)
                    <div style="display: flex; align-items: baseline; gap: 1.5rem;">
                        <span style="font-size: 2rem; font-weight: 700;">&#8377;{{ number_format($product->active_price, 2) }}</span>
                        <span style="font-size: 1.2rem; color: var(--zyra-silver); text-decoration: line-through;">&#8377;{{ number_format($product->price, 2) }}</span>
                    </div>
                @else
                    <span style="font-size: 2rem; font-weight: 700;">&#8377;{{ number_format($product->active_price, 2) }}</span>
                @endif
                <div style="font-size: 0.75rem; color: var(--zyra-silver); margin-top: 0.5rem;">* This price does not include any taxes</div>
            </div>

            <!-- Form -->
            <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm" style="margin-bottom: 4rem;">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div style="margin-bottom: 3rem;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800;">Available Sizes</h4>
                        <button type="button" onclick="openSizeChart()" style="background: none; border: none; font-size: 0.75rem; color: var(--zyra-silver); text-transform: uppercase; text-decoration: underline; cursor: pointer;">Size Guide</button>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                        @forelse($product->productSizes as $size)
                            <label style="flex: 1; min-width: 80px; position: relative; cursor: pointer;">
                                <input type="radio" name="size" value="{{ $size->size }}" required style="display: none;" onchange="updateSizeUI(this)">
                                <div class="size-btn" style="padding: 1rem; border: 1px solid var(--border-color); text-align: center; font-weight: 700; transition: all 0.3s ease;">
                                    {{ $size->size }}
                                </div>
                            </label>
                        @empty
                            <div style="padding: 1rem; background: #F9F9F9; border: 1px solid #EEE; border-radius: 4px; font-size: 0.85rem; color: #666; width: 100%;">
                                Bespoke sizing available. Please contact our concierge.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div style="margin-bottom: 3rem;">
                    <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; margin-bottom: 1.5rem;">Quantity</h4>
                    <div style="display: flex; align-items: center; gap: 0; border: 1px solid var(--border-color); width: fit-content; border-radius: 4px; overflow: hidden;">
                        <button type="button" onclick="changeQty(-1)" style="width: 50px; height: 50px; background: transparent; border: none; font-size: 1.2rem; cursor: pointer; color: var(--zyra-white);">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" readonly style="width: 50px; height: 50px; background: transparent; border: none; text-align: center; font-weight: 700; font-size: 1.1rem; color: var(--zyra-white);">
                        <button type="button" onclick="changeQty(1)" style="width: 50px; height: 50px; background: transparent; border: none; font-size: 1.2rem; cursor: pointer; color: var(--zyra-white);">+</button>
                    </div>
                </div>

                <div style="display: flex; gap: 1.5rem; width: 100%;">
                    <button type="submit" class="btn-premium" style="flex: 1; text-align: center; font-size: 1rem; padding: 1.25rem;">Add to Atelier Bag</button>
                    <button type="button" class="wishlist-heart-btn" onclick="toggleWishlist(event, {{ $product->id }}, this)" aria-label="Toggle Wishlist" style="background: var(--zyra-charcoal); border: 1px solid var(--border-color); border-radius: 4px; width: 60px; height: 58px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <svg class="heart-icon {{ \App\Models\Wishlist::isProductWishlisted($product->id) ? 'active' : '' }}" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" style="transition: all 0.3s ease;">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Accordeon Info -->
            <div style="border-top: 1px solid var(--border-color);">
                <div class="info-item" style="padding: 1.5rem 0; border-bottom: 1px solid var(--border-color);">
                    <h3 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">The Silhouette</h3>
                    <p style="color: var(--zyra-silver); line-height: 1.8; font-size: 0.95rem;">{{ $product->description }}</p>
                </div>
                <div class="info-item" style="padding: 1.5rem 0; border-bottom: 1px solid var(--border-color); display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); display: block; margin-bottom: 0.5rem;">Reference SKU</span>
                        <span style="font-weight: 700;">{{ $product->sku }}</span>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); display: block; margin-bottom: 0.5rem;">Crafting Origin</span>
                        <span style="font-weight: 700;">Hand-finished Atelier</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .size-btn:hover {
        border-color: var(--zyra-gold);
    }
    .size-btn.active {
        background: var(--zyra-white);
        color: var(--zyra-black);
        border-color: var(--zyra-white);
    }

    /* Image Slider Styles */
    .carousel-container {
        position: relative;
        overflow: hidden;
    }

    .carousel-slide {
        transition: opacity 0.5s ease-in-out;
    }

    .carousel-arrow {
        transition: all 0.3s ease;
        opacity: 0.8;
    }

    .carousel-arrow:hover {
        opacity: 1;
        background: rgba(0,0,0,0.8) !important;
        transform: translateY(-50%) scale(1.1);
    }

    .thumbnail-item {
        transition: all 0.3s ease;
    }

    .thumbnail-item:hover {
        opacity: 1 !important;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .thumbnail-item.active {
        box-shadow: 0 0 0 2px var(--zyra-gold);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .carousel-arrow {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
        
        .thumbnail-gallery {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 0.5rem !important;
        }
    }

    @media (max-width: 480px) {
        .carousel-arrow {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }
        
        .thumbnail-gallery {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    /* Loading state */
    .carousel-slide img {
        transition: transform 0.3s ease;
    }

    .carousel-slide:hover img {
        transform: scale(1.02);
    }
</style>
<script>
    // Image Slider Functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    const totalSlides = slides.length;

    function showSlide(index) {
        if (totalSlides === 0) return;
        
        // Hide all slides
        slides.forEach((slide, i) => {
            slide.style.opacity = i === index ? '1' : '0';
        });
        
        // Update thumbnails
        thumbnails.forEach((thumb, i) => {
            if (i === index) {
                thumb.style.borderColor = 'var(--zyra-gold)';
                thumb.style.opacity = '1';
            } else {
                thumb.style.borderColor = 'var(--border-color)';
                thumb.style.opacity = '0.7';
            }
        });
        
        // Update counter
        const counter = document.getElementById('imageCounter');
        if (counter) {
            counter.textContent = `${index + 1} / ${totalSlides}`;
        }
        
        currentSlide = index;
    }

    function changeSlide(direction) {
        if (totalSlides === 0) return;
        
        let newSlide = currentSlide + direction;
        if (newSlide >= totalSlides) newSlide = 0;
        if (newSlide < 0) newSlide = totalSlides - 1;
        
        showSlide(newSlide);
    }

    function goToSlide(index) {
        if (totalSlides === 0) return;
        showSlide(index);
    }

    // Auto-play functionality (optional)
    let autoPlayInterval;
    function startAutoPlay() {
        if (totalSlides <= 1) return;
        autoPlayInterval = setInterval(() => changeSlide(1), 5000);
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') changeSlide(-1);
        if (e.key === 'ArrowRight') changeSlide(1);
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    const carouselContainer = document.querySelector('.carousel-container');
    if (carouselContainer) {
        carouselContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        carouselContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                changeSlide(1); // Swipe left, go to next
            } else {
                changeSlide(-1); // Swipe right, go to previous
            }
        }
    }

    // Initialize slider
    document.addEventListener('DOMContentLoaded', () => {
        showSlide(0);
        // Optional: Start auto-play
        // startAutoPlay();
        
        // Stop auto-play on hover
        const carousel = document.querySelector('.carousel-container');
        if (carousel) {
            carousel.addEventListener('mouseenter', stopAutoPlay);
            carousel.addEventListener('mouseleave', startAutoPlay);
        }
    });

    function updateSizeUI(radio) {
        document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
        radio.nextElementSibling.classList.add('active');
    }

    function changeQty(delta) {
        const input = document.getElementById('quantity');
        const newVal = parseInt(input.value) + delta;
        if (newVal >= 1 && newVal <= 10) {
            input.value = newVal;
        }
    }

    // Handle Add to Cart via AJAX
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        
        submitBtn.disabled = true;
        submitBtn.innerText = 'Adding to Bag...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200 || res.status === 201) {
                showNotification(res.body.message, 'success');
                if (typeof updateCartCount === 'function') updateCartCount();
            } else if (res.status === 401) {
                window.location.href = res.body.redirect || '/login';
            } else {
                showNotification(res.body.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            showNotification('Something went wrong. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        });
    });

    function showNotification(message, type) {
        let toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed; top: 100px; right: 20px; 
            padding: 1.25rem 2.5rem; border-radius: 4px; z-index: 10001; 
            color: white; font-weight: 600; font-size: 0.9rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: translateX(120%); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: ${type === 'success' ? 'var(--success)' : 'var(--danger)'};
        `;
        toast.innerText = message;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function openSizeChart() {
        document.getElementById('sizeChartModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeSizeChart() {
        document.getElementById('sizeChartModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('sizeChartModal');
        if (event.target == modal) {
            closeSizeChart();
        }
    }
</script>

<!-- Size Chart Modal -->
<div id="sizeChartModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); z-index: 10000; justify-content: center; align-items: center; padding: 2rem;">
    <div style="background: white; width: 100%; max-width: 600px; padding: 3rem; position: relative; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <button onclick="closeSizeChart()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; font-size: 1.5rem; color: #111; cursor: pointer;">&times;</button>
        
        <h2 style="font-size: 2.2rem; font-family: 'Syne', sans-serif; margin-bottom: 1rem; color: #111;">Size Guide</h2>
        <p style="color: #666; margin-bottom: 2.5rem; font-size: 0.95rem; line-height: 1.6;">Our collections are precision-engineered for a signature silhouette. All measurements are in inches.</p>
        
        <div style="overflow-x: auto; margin-bottom: 2rem; border: 1px solid #EEE; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #F9F9F9; border-bottom: 1px solid #EEE;">
                        <th style="padding: 1.25rem 1rem; font-weight: 800; color: #111; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Size</th>
                        <th style="padding: 1.25rem 1rem; font-weight: 800; color: #111; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Chest</th>
                        <th style="padding: 1.25rem 1rem; font-weight: 800; color: #111; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Waist</th>
                        <th style="padding: 1.25rem 1rem; font-weight: 800; color: #111; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Hip</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 700; color: #111;">XS</td>
                        <td style="padding: 1rem; color: #555;">32 - 34</td>
                        <td style="padding: 1rem; color: #555;">26 - 28</td>
                        <td style="padding: 1rem; color: #555;">33 - 35</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 700; color: #111;">S</td>
                        <td style="padding: 1rem; color: #555;">34 - 36</td>
                        <td style="padding: 1rem; color: #555;">28 - 30</td>
                        <td style="padding: 1rem; color: #555;">35 - 37</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 700; color: #111;">M</td>
                        <td style="padding: 1rem; color: #555;">36 - 38</td>
                        <td style="padding: 1rem; color: #555;">30 - 32</td>
                        <td style="padding: 1rem; color: #555;">37 - 39</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 700; color: #111;">L</td>
                        <td style="padding: 1rem; color: #555;">38 - 40</td>
                        <td style="padding: 1rem; color: #555;">32 - 34</td>
                        <td style="padding: 1rem; color: #555;">39 - 41</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 700; color: #111;">XL</td>
                        <td style="padding: 1rem; color: #555;">40 - 42</td>
                        <td style="padding: 1rem; color: #555;">34 - 36</td>
                        <td style="padding: 1rem; color: #555;">41 - 43</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div style="padding: 1.5rem; background: #FFF9F2; border: 1px solid #FFE4C4; border-radius: 6px; font-size: 0.85rem; color: #8B4513; line-height: 1.6;">
            <strong style="color: #5D2E0B;">Atelier Advice:</strong> If you are between sizes, we recommend selecting the larger size for a signature luxury drape.
        </div>
    </div>
</div>
@endsection
