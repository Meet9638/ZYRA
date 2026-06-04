@extends('layouts.app')

@section('title', 'My Profile - ZYRA')

@push('head-scripts')
<style>
    .profile-card {
        background: var(--zyra-charcoal);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
    @media (min-width: 768px) {
        .profile-card {
            flex-direction: row;
            align-items: center;
            gap: 2.5rem;
        }
    }
    .profile-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose));
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        font-weight: 700;
        font-family: 'Syne', sans-serif;
        box-shadow: 0 8px 20px rgba(232, 111, 28, 0.25);
        flex-shrink: 0;
        margin-bottom: 1.5rem;
    }
    @media (min-width: 768px) {
        .profile-avatar { margin-bottom: 0; }
    }
    .profile-info {
        flex-grow: 1;
    }
    .profile-info h2 {
        font-size: 2rem;
        margin-bottom: 0.25rem;
        color: var(--zyra-white);
    }
    .profile-info .text-muted {
        color: var(--zyra-silver);
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .profile-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        width: 100%;
    }
    .detail-item {
        display: flex;
        flex-direction: column;
    }
    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--zyra-silver);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .detail-value {
        font-size: 1.05rem;
        font-weight: 500;
        color: var(--zyra-white);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .stat-card {
        background: var(--zyra-charcoal);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2rem 1.5rem;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(232, 111, 28, 0.08);
        border-color: rgba(232, 111, 28, 0.3);
    }
    .stat-value {
        font-size: 2.4rem;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        background: linear-gradient(135deg, var(--zyra-gold), var(--zyra-rose));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        line-height: 1.2;
        word-break: break-all;
        overflow-wrap: break-word;
    }
    .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--zyra-silver);
        font-weight: 600;
    }
    
    .orders-section {
        background: var(--zyra-charcoal);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }
    .section-header h3 {
        font-size: 1.5rem;
        margin: 0;
        color: var(--zyra-white);
    }
    .view-all-link {
        color: var(--zyra-gold);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: opacity 0.3s ease;
    }
    .view-all-link:hover {
        opacity: 0.8;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 0;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        transition: padding 0.3s ease, background 0.3s ease;
    }
    .order-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .order-item:hover {
        padding-left: 1rem;
        padding-right: 1rem;
        background: rgba(232, 111, 28, 0.03);
        border-radius: 8px;
    }
    .order-info strong {
        display: block;
        color: var(--zyra-white);
        font-size: 1.15rem;
        margin-bottom: 0.25rem;
    }
    .order-info span {
        color: var(--zyra-silver);
        font-size: 0.9rem;
    }
    .order-status {
        text-align: right;
    }
    .order-price {
        font-weight: 700;
        color: var(--zyra-white);
        display: block;
        font-size: 1.2rem;
        margin-bottom: 0.35rem;
    }
    .status-badge {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        background: rgba(232, 111, 28, 0.1);
        color: var(--zyra-gold);
        font-weight: 600;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
    }
    .empty-icon {
        font-size: 3.5rem;
        opacity: 0.5;
        margin-bottom: 1.5rem;
        display: inline-block;
    }
    .empty-state h3 {
        color: var(--zyra-white);
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
    }
    .empty-state p {
        color: var(--zyra-silver);
        margin-bottom: 2rem;
        font-size: 1rem;
    }
    
    .alert-primary {
        background: rgba(232, 111, 28, 0.05);
        border: 1px solid rgba(232, 111, 28, 0.2);
        color: var(--zyra-gold);
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .alert-primary a {
        color: var(--zyra-gold);
        font-weight: 700;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-grid">
        <!-- Sidebar Navigation -->
        <div class="dashboard-sidebar">
            <a href="{{ route('users.profile') }}" class="active">
                👤 Profile Overview
            </a>
            <a href="{{ route('users.edit') }}">
                ✏️ Edit Profile
            </a>
            <a href="{{ route('users.orders') }}">
                📦 My Orders
            </a>
            <a href="{{ route('returns.index') }}">
                ↩️ Returns
            </a>
        </div>

        <!-- Main Content -->
        <div>
            <!-- User Info Card -->
            <div class="profile-card">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="profile-info">
                    <h2>{{ $user->name }}</h2>
                    <div class="text-muted">{{ $user->email }}</div>
                    <div class="text-muted" style="font-size: 0.85rem;">Member since {{ $user->created_at->format('M Y') }}</div>
                    
                    <div class="profile-details">
                        @if($user->gender)
                            <div class="detail-item">
                                <span class="detail-label">Gender</span>
                                <span class="detail-value">{{ ucfirst($user->gender) }}</span>
                            </div>
                        @endif
                        @if($user->phone)
                            <div class="detail-item">
                                <span class="detail-label">Phone</span>
                                <span class="detail-value">{{ $user->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_orders'] }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>

                <div class="stat-card">
                    <div class="stat-value">₹{{ number_format($stats['total_spent'], 0) }}</div>
                    <div class="stat-label">Total Spent</div>
                </div>

                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_returns'] }}</div>
                    <div class="stat-label">Returns</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="orders-section">
                <div class="section-header">
                    <h3>Recent Orders</h3>
                    @if($user->orders->count() > 0)
                        <a href="{{ route('users.orders') }}" class="view-all-link">View All &rarr;</a>
                    @endif
                </div>
                
                @if($user->orders->count() > 0)
                    <div>
                        @foreach($user->orders->take(5) as $order)
                            <a href="{{ route('orders.show', $order) }}" class="order-item">
                                <div class="order-info">
                                    <strong>{{ $order->order_number }}</strong>
                                    <span>{{ $order->orderItems->count() }} item(s) &bull; {{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="order-status">
                                    <span class="order-price">₹{{ number_format($order->total_amount, 2) }}</span>
                                    <span class="status-badge">{{ $order->status }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <span class="empty-icon">📦</span>
                        <h3>No Orders Yet</h3>
                        <p>Start shopping to see your orders here</p>
                        <a href="{{ route('shop') }}" class="btn btn-primary">
                            Browse Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
