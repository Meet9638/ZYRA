
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ZYRA - Premium Fashion Experience')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    @stack('head-scripts')

    <style>
        /* Premium Wishlist Heart Animations */
        .wishlist-heart-btn:hover {
            transform: scale(1.15);
            background: #ffffff !important;
            box-shadow: 0 6px 20px rgba(232, 111, 28, 0.15) !important;
        }
        .wishlist-heart-btn:active {
            transform: scale(0.9);
        }
        .heart-icon {
            color: var(--zyra-silver);
        }
        .heart-icon.active {
            fill: url(#heart-grad) !important;
            stroke: var(--zyra-gold) !important;
            color: var(--zyra-gold) !important;
            filter: drop-shadow(0 2px 5px rgba(232, 111, 28, 0.3));
            animation: heartBeat 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes heartBeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Navigation -->
    <nav class="main-nav" id="mainNav">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo-container" style="display: flex; align-items: center; text-decoration: none; height: 100%;">
                <img src="{{ asset('images/logo.png') }}" alt="ZYRA Logo" style="height: 60px; width: auto; object-fit: contain;">
            </a>
            <div class="nav-links" id="navLinks">
                <a href="{{ route('shop') }}" class="nav-link">Shop</a>
                <a href="{{ route('categories.index') }}" class="nav-link">Categories</a>
                <a href="{{ route('track-order.index') }}" class="nav-link">Track Order</a>
                
                @auth
                    <a href="{{ route('users.profile') }}" class="nav-link">Profile</a>
                    <a href="{{ route('orders.index') }}" class="nav-link">Orders</a>
                    <a href="{{ route('returns.index') }}" class="nav-link">Returns</a>
                @endauth
                
                <a href="{{ route('wishlist.index') }}" class="cart-badge" style="margin-right: 1.5rem;" title="Wishlist">
                    ❤️
                    <span class="cart-count" id="wishlistCount" style="background: var(--zyra-gold);">0</span>
                </a>

                <a href="{{ route('cart.index') }}" class="cart-badge">
                    🛒
                    <span class="cart-count" id="cartCount">0</span>
                </a>
                
                @guest
                    <a href="{{ route('login') }}" class="btn-nav">Login</a>
                @else
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-nav">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="container" style="padding-bottom: 0;">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container" style="padding-bottom: 0;">
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="container" style="padding-bottom: 0;">
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="container" style="padding-bottom: 0;">
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>ZYRA</h3>
                <p>
                    Redefining luxury fashion through curated excellence and uncompromising craftsmanship for the modern lifestyle.
                </p>
            </div>
            <div class="footer-section">
                <h3>Shop</h3>
                <ul>
                    <li><a href="{{ route('shop') }}?gender=women">Women's Collection</a></li>
                    <li><a href="{{ route('shop') }}?gender=men">Men's Collection</a></li>
                    <li><a href="{{ route('shop') }}?sort=newest">New Arrivals</a></li>
                    <li><a href="{{ route('shop') }}?featured=true">Featured</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <ul>
                    <li><a href="{{ route('size-guide') }}">Size Guide</a></li>
                    <li><a href="{{ route('returns.index') }}">Returns & Exchanges</a></li>

                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Company</h3>
                <ul>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} ZYRA. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainNav = document.getElementById('mainNav');
        const navLinks = document.getElementById('navLinks');

        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('mobile-open');
            navLinks.classList.toggle('mobile-open');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mainNav.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                mobileMenuToggle.classList.remove('active');
                mainNav.classList.remove('mobile-open');
                navLinks.classList.remove('mobile-open');
            }
        });

        // Update cart count
        function updateCartCount() {
            fetch('{{ route('cart.count') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cartCount').textContent = data.count;
                });
        }

        // Update wishlist count
        function updateWishlistCount() {
            fetch('{{ route('wishlist.count') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('wishlistCount').textContent = data.count;
                })
                .catch(err => console.error('Error fetching wishlist count:', err));
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });

            // Update counts on page load
            updateCartCount();
            updateWishlistCount();
        });

        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Toggle wishlist items via AJAX
        function toggleWishlist(event, productId, btnElement) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const heartIcon = btnElement.querySelector('.heart-icon');
            
            // Visual loading state / optimistic updates
            btnElement.style.pointerEvents = 'none';
            heartIcon.style.opacity = '0.5';

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
                if (res.status === 200 || res.status === 201) {
                    const data = res.body;
                    if (data.action === 'added') {
                        heartIcon.classList.add('active');
                    } else {
                        heartIcon.classList.remove('active');
                    }
                    
                    // Show notification
                    showNotification(data.message, 'success');
                    
                    // Update global count badge
                    if (document.getElementById('wishlistCount')) {
                        document.getElementById('wishlistCount').textContent = data.wishlist_count;
                    }
                } else {
                    showNotification(res.body.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                showNotification('Something went wrong. Please try again.', 'error');
            })
            .finally(() => {
                btnElement.style.pointerEvents = 'auto';
                heartIcon.style.opacity = '1';
            });
        }

        // Global toast notification function
        if (typeof showNotification !== 'function') {
            window.showNotification = function(message, type) {
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
        }
    </script>
    
    <!-- Global Heart Gradient Definition -->
    <svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
      <linearGradient id="heart-grad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="var(--zyra-gold)" />
        <stop offset="100%" stop-color="var(--zyra-rose)" />
      </linearGradient>
    </svg>

    @stack('scripts')
</body>
</html>
