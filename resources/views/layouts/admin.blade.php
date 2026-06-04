<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - ZYRA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
   
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @stack('styles')
    @stack('head-scripts')
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo" style="text-align: center; padding: 1.5rem 1rem;">
            <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; justify-content: center; height: 50px; width: 100%; overflow: hidden; margin-bottom: 5px;">
                <img src="{{ asset('images/logo.png') }}" alt="ZYRA Logo" style="height: 100%; width: auto; object-fit: contain;">
            </a>
            <p   class="auto-style-0204" style="font-size: 0.65rem; color: var(--zyra-silver); letter-spacing: 0.2em; text-transform: uppercase;">Admin Panel</p>
        </div>

        <div class="admin-nav-section">
            <div class="admin-nav-title">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="admin-nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="admin-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span class="admin-nav-icon">👕</span>
                <span>Products</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span class="admin-nav-icon">📦</span>
                <span>Orders</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="admin-nav-icon">👥</span>
                <span>Customers</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="admin-nav-icon">📂</span>
                <span>Categories</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="admin-nav-icon">⚙️</span>
                <span>Settings</span>
            </a>
            <a href="{{ route('admin.returns.index') }}" class="admin-nav-item {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                <span class="admin-nav-icon">↩️</span>
                <span>Returns</span>
            </a>
            <a href="{{ route('admin.analytics') }}" class="admin-nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                <span class="admin-nav-icon">📈</span>
                <span>Analytics</span>
            </a>
        </div>

        <div class="admin-nav-section">
            <div class="admin-nav-title">Account</div>
            <a href="{{ route('home') }}" class="admin-nav-item" target="_blank">
                <span class="admin-nav-icon">🏠</span>
                <span>View Site</span>
            </a>
            <a href="#" class="admin-nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="admin-nav-icon">🚪</span>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main-content">
        <div class="admin-top-bar">
            <h1 class="admin-page-title">@yield('page-title', 'Dashboard')</h1>
            <div class="admin-user-profile">
                <div   class="auto-style-0050">
                    <div   class="admin-user-name">{{ auth('admin')->user()->name ?? 'Admin' }}</div>
                    <div   class="admin-user-email">{{ auth('admin')->user()->email ?? 'admin@noirair.com' }}</div>
                </div>
                <div class="admin-user-avatar">{{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 2)) }}</div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul   class="auto-style-0208">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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
        });
    </script>
    
    @stack('scripts')
</body>
</html>

