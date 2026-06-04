@extends('layouts.app')

@section('title', 'Return Product - ZYRA')

@section('content')
<div class="container" style="padding: 120px 0 80px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 4rem;">
            <h1 style="font-size: 3rem; font-family: 'Syne', sans-serif; margin-bottom: 1rem;">Return <span class="text-gradient">Request</span></h1>
            <p style="color: var(--zyra-silver);">Please provide a reason for your return. We'll review it and get back to you shortly.</p>
        </div>

        <div class="glass-card" style="padding: 3rem;">
            <div style="display: flex; gap: 2rem; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="width: 120px; height: 150px; background: var(--zyra-gray); border-radius: 4px; overflow: hidden; flex-shrink: 0;">
                    @if($orderItem->product->main_image)
                        <img src="{{ Str::startsWith($orderItem->product->main_image, 'http') ? $orderItem->product->main_image : asset('storage/' . $orderItem->product->main_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @endif
                </div>
                <div>
                    <h3 style="font-size: 1.5rem; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">{{ $orderItem->product->name }}</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; color: var(--zyra-silver);">
                        <span>Order #: {{ $orderItem->order->order_number }}</span>
                        <span>Size: {{ $orderItem->size }}</span>
                        <span>Price: ₹{{ number_format($orderItem->price, 2) }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('returns.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_item_id" value="{{ $orderItem->id }}">

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 1rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Reason for Return</label>
                    <select name="reason" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 1rem; border-radius: 4px; outline: none; appearance: none;">
                        <option value="" disabled selected style="background: var(--zyra-black);">Select a reason</option>
                        <option value="too_small" style="background: var(--zyra-black);">Too small</option>
                        <option value="too_large" style="background: var(--zyra-black);">Too large</option>
                        <option value="wrong_item" style="background: var(--zyra-black);">Wrong item received</option>
                        <option value="damaged" style="background: var(--zyra-black);">Received damaged</option>
                        <option value="quality_issue" style="background: var(--zyra-black);">Quality issue</option>
                        <option value="not_as_described" style="background: var(--zyra-black);">Not as described</option>
                        <option value="other" style="background: var(--zyra-black);">Other</option>
                    </select>
                    @error('reason')
                        <p style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 1rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Detailed Description (Optional)</label>
                    <textarea name="detailed_reason" rows="4" placeholder="Tell us more about the issue..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 1rem; border-radius: 4px; outline: none; resize: vertical;"></textarea>
                    @error('detailed_reason')
                        <p style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 3rem;">
                    <label style="display: block; margin-bottom: 1rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Preferred Replacement Size (If applicable)</label>
                    <input type="text" name="preferred_replacement_size" placeholder="e.g. M, L, XL" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 1rem; border-radius: 4px; outline: none;">
                    @error('preferred_replacement_size')
                        <p style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: flex; gap: 2rem; align-items: center;">
                    <button type="submit" class="btn-premium">Submit Return Request</button>
                    <a href="{{ route('orders.show', $orderItem->order) }}" style="text-decoration: none; color: var(--zyra-silver); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
