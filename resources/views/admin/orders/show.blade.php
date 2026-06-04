@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)
@section('page-title', 'Order Details')

@push('styles')
<style>
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }
    .order-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--zyra-gold);
        font-family: 'Syne', sans-serif;
    }
    .order-date {
        color: var(--zyra-silver);
        font-size: 0.95rem;
    }
    
    .status-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.03);
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .status-label {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--zyra-silver);
        font-size: 0.85rem;
    }
    .status-select {
        background: var(--zyra-black);
        color: var(--zyra-white);
        border: 1px solid var(--zyra-gold);
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        outline: none;
    }
    .status-select:focus {
        box-shadow: 0 0 10px rgba(232, 111, 28, 0.2);
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: var(--zyra-charcoal);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
    }
    .info-card h4 {
        color: var(--zyra-gold);
        margin-bottom: 1rem;
        font-family: 'Syne', sans-serif;
        font-size: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 0.5rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    .info-label {
        color: var(--zyra-silver);
    }
    .info-value {
        font-weight: 500;
        color: var(--zyra-white);
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--zyra-charcoal);
        border-radius: 8px;
        overflow: hidden;
    }
    .items-table th {
        background: rgba(255, 255, 255, 0.05);
        color: var(--zyra-silver);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        text-align: left;
    }
    .items-table td {
        padding: 1.25rem 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        vertical-align: middle;
    }
    .product-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .product-thumb {
        width: 60px;
        height: 80px;
        border-radius: 4px;
        object-fit: cover;
    }
    .product-name {
        font-weight: 600;
        color: var(--zyra-white);
        margin-bottom: 0.25rem;
    }
    .ai-badge {
        display: inline-block;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        background: rgba(232, 111, 28, 0.15);
        color: var(--zyra-gold);
        margin-top: 0.25rem;
    }
    
    .totals-section {
        margin-top: 2rem;
        display: flex;
        justify-content: flex-end;
    }
    .totals-box {
        width: 300px;
        background: var(--zyra-charcoal);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
    }
    .total-row.grand-total {
        font-size: 1.25rem;
        color: var(--zyra-gold);
        font-weight: 700;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1rem;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="order-header">
    <div>
        <div class="order-number">Order #{{ $order->order_number }}</div>
        <div class="order-date">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</div>
    </div>
    
    <div class="status-container">
        <span class="status-label">Update Status:</span>
        <select class="status-select" id="orderStatusSelector" data-order-id="{{ $order->id }}">
            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>
        <span id="statusLoader" style="display: none; color: var(--zyra-gold);">Saving...</span>
    </div>
</div>

<div class="grid-2">
    <div class="info-card">
        <h4>Customer Information</h4>
        <div class="info-row">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $order->user->name ?? 'Guest User' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value">{{ $order->user->email ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value">{{ $order->user->phone ?? 'N/A' }}</span>
        </div>
    </div>
    
    <div class="info-card">
        <h4>Shipping Details</h4>
        <div class="info-row">
            <span class="info-label">Address</span>
            <span class="info-value" style="text-align: right; max-width: 250px;">
                {{ $order->shipping_address ?? 'Address not provided during checkout.' }}
            </span>
        </div>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->orderItems as $item)
        <tr>
            <td>
                <div class="product-cell">
                    @if($item->product->main_image)
                        <img src="{{ Storage::url($item->product->main_image) }}" class="product-thumb">
                    @else
                        <div class="product-thumb" style="background: #333; display:flex; align-items:center; justify-content:center; color:#fff;">NO<br>IMG</div>
                    @endif
                    <div>
                        <div class="product-name">{{ $item->product->name }}</div>
                        @if($item->followed_ai_recommendation)
                            <div class="ai-badge">✓ AI Size Followed</div>
                        @elseif($item->ai_recommended_size)
                            <div class="ai-badge" style="background: rgba(255,255,255,0.1); color: #888;">AI Suggested: {{ $item->ai_recommended_size }}</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <strong style="color: var(--zyra-gold);">{{ strtoupper($item->size) }}</strong>
            </td>
            <td>₹{{ number_format($item->price, 2) }}</td>
            <td>{{ $item->quantity }}</td>
            <td><strong>₹{{ number_format($item->total_price, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals-section">
    <div class="totals-box">
        <div class="info-row">
            <span class="info-label">Subtotal</span>
            <span class="info-value">₹{{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tax</span>
            <span class="info-value">₹{{ number_format($order->tax, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Shipping</span>
            <span class="info-value">₹{{ number_format($order->shipping_cost, 2) }}</span>
        </div>
        <div class="info-row grand-total">
            <span class="info-label" style="color: var(--zyra-gold);">Total</span>
            <span class="info-value">₹{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('orderStatusSelector').addEventListener('change', function() {
    const select = this;
    const loader = document.getElementById('statusLoader');
    const orderId = select.dataset.orderId;
    const newStatus = select.value;
    
    select.disabled = true;
    loader.style.display = 'inline-block';
    loader.innerText = 'Saving...';
    
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            loader.innerText = '✓ Saved!';
            loader.style.color = 'var(--success)';
            setTimeout(() => {
                loader.style.display = 'none';
                loader.style.color = 'var(--zyra-gold)';
            }, 2000);
        } else {
            throw new Error(data.message || 'Error updating status');
        }
    })
    .catch(error => {
        alert('Failed to update status: ' + error.message);
        loader.style.display = 'none';
        // Reset to original (could be improved to actually reset to old value)
    })
    .finally(() => {
        select.disabled = false;
    });
});
</script>
@endpush
@endsection
