@extends('layouts.admin')

@section('page-title', 'Detailed Analytics')

@section('content')

<div class="admin-header" style="margin-bottom: 2rem;">
    <h2>Performance Overview ({{ ucfirst($period) }})</h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Overview Stats -->
    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="font-size: 0.9rem; color: var(--zyra-silver); margin-bottom: 0.5rem; text-transform: uppercase;">Total Rev. (All Time)</h3>
        <p style="font-size: 1.5rem; color: var(--success); font-weight: bold;">&#8377;{{ number_format($analytics['overview']['total_revenue'], 2) }}</p>
    </div>
    
    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="font-size: 0.9rem; color: var(--zyra-silver); margin-bottom: 0.5rem; text-transform: uppercase;">Period Revenue</h3>
        <p style="font-size: 1.5rem; color: var(--zyra-gold); font-weight: bold;">&#8377;{{ number_format($analytics['sales']['period_revenue'], 2) }}</p>
    </div>

    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="font-size: 0.9rem; color: var(--zyra-silver); margin-bottom: 0.5rem; text-transform: uppercase;">Period Orders</h3>
        <p style="font-size: 1.5rem; color: var(--zyra-white); font-weight: bold;">{{ number_format($analytics['sales']['period_orders']) }}</p>
    </div>

    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="font-size: 0.9rem; color: var(--zyra-silver); margin-bottom: 0.5rem; text-transform: uppercase;">New Customers</h3>
        <p style="font-size: 1.5rem; color: var(--ai-badge); font-weight: bold;">{{ number_format($analytics['customers']['new_customers']) }}</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- Top Products -->
    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; color: var(--zyra-gold);">Top Selling Products</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); color: var(--zyra-silver); text-align: left;">
                    <th style="padding: 0.5rem 0;">Product</th>
                    <th style="padding: 0.5rem 0;">Sold</th>
                    <th style="padding: 0.5rem 0; text-align: right;">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analytics['products']['top_selling'] as $product)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 0.75rem 0; color: var(--zyra-white);">{{ $product->name }}</td>
                    <td style="padding: 0.75rem 0; color: var(--zyra-silver);">{{ $product->total_sold }}</td>
                    <td style="padding: 0.75rem 0; text-align: right; color: var(--success);">&#8377;{{ number_format($product->total_revenue, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="padding: 1rem 0; color: var(--muted-text);">No products sold in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; color: var(--zyra-gold);">Store Status</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--zyra-silver);">Total Products</span>
                <span style="color: var(--zyra-white); font-weight: bold; font-size: 1.2rem;">{{ \App\Models\Product::count() }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--zyra-silver);">Active Categories</span>
                <span style="color: var(--success); font-weight: bold; font-size: 1.2rem;">{{ \App\Models\Category::where('is_active', true)->count() }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--zyra-silver);">Total Registered Users</span>
                <span style="color: var(--zyra-gold); font-weight: bold; font-size: 1.2rem;">{{ \App\Models\User::count() }}</span>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

    <!-- Inventory Alerts -->
    <div class="admin-card" style="padding: 1.5rem; border-top: 3px solid var(--warning);">
        <h3 style="margin-bottom: 1rem; color: var(--warning);">Inventory Alerts</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span style="color: var(--zyra-silver);">Low Stock Items (< 10)</span>
            <span style="color: var(--zyra-white); font-weight: bold;">{{ $analytics['products']['low_stock'] }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--zyra-silver);">Out of Stock Items</span>
            <span style="color: var(--danger); font-weight: bold;">{{ $analytics['products']['out_of_stock'] }}</span>
        </div>
        <div style="margin-top: 1.5rem;">
            <a href="{{ route('admin.products.index') }}" class="btn-primary" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Manage Inventory</a>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="admin-card" style="padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; color: var(--zyra-gold);">Top Customers by Spending</h3>
        @forelse($analytics['customers']['top_customers'] as $customer)
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding: 0.5rem 0;">
                <div>
                    <div style="color: var(--zyra-white); font-weight: 500;">{{ $customer->name }}</div>
                    <div style="color: var(--muted-text); font-size: 0.8rem;">{{ $customer->total_orders }} orders</div>
                </div>
                <div style="color: var(--success); font-weight: bold;">
                    &#8377;{{ number_format($customer->total_spent, 2) }}
                </div>
            </div>
        @empty
            <div style="color: var(--muted-text); padding: 1rem 0;">No purchase data available.</div>
        @endforelse
    </div>
</div>

@endsection
