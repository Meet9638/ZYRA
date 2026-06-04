@extends('layouts.app')

@section('title', 'Track Order - ZYRA')

@section('content')
<div style="background: var(--zyra-black); min-height: 100vh; padding-top: 6rem; padding-bottom: 10rem;">
    <!-- Hero Header -->
    <div class="container" style="text-align: center; margin-bottom: 6rem;">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 1.5rem; display: block;">Luxury Concierge</span>
        <h1 style="font-size: clamp(2.5rem, 6vw, 4.5rem); font-family: 'Syne', sans-serif; line-height: 1;" class="text-gradient">Track Your Journey</h1>
        <p style="color: var(--zyra-silver); max-width: 600px; margin: 2rem auto 0; font-size: 1.1rem; line-height: 1.8;">Monitor the real-time progress of your curated ZYRA selections as they make their way to your atelier.</p>
    </div>

    <div class="container" style="max-width: 900px;">
        <!-- Search Box -->
        <div class="glass-card" style="padding: 4rem; margin-bottom: 5rem;">
            <form action="{{ route('track-order.track') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; margin-bottom: 3rem;">
                    <div>
                        <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); display: block; margin-bottom: 1rem;">Order Number</label>
                        <input type="text" name="order_number" value="{{ old('order_number') }}" placeholder="ORD-123456" required style="width: 100%; padding: 1.2rem; background: var(--zyra-gray); border: 1px solid var(--border-color); color: var(--zyra-white); font-weight: 600; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); display: block; margin-bottom: 1rem;">Registered Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="atelier@zyra.com" required style="width: 100%; padding: 1.2rem; background: var(--zyra-gray); border: 1px solid var(--border-color); color: var(--zyra-white); font-weight: 600; border-radius: 4px;">
                    </div>
                </div>
                <button type="submit" class="btn-premium" style="width: 100%; text-align: center; border-radius: 4px;">Locate Order</button>
            </form>
        </div>

        @if(isset($order))
        <!-- Tracking Results -->
        <div class="glass-card" style="padding: 5rem 4rem; border-top: 4px solid var(--zyra-gold);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
                <div>
                    <h2 style="font-size: 2.5rem; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">#{{ $order->order_number }}</h2>
                    <p style="color: var(--zyra-silver); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em;">Order Commmenced: {{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; padding: 0.8rem 2rem; background: var(--zyra-white); color: var(--zyra-black); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.2em; border-radius: 50px;">{{ $order->status }}</span>
                </div>
            </div>

            <!-- Enhanced Timeline -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; position: relative; margin-bottom: 6rem;">
                <div style="position: absolute; top: 15px; left: 0; width: 100%; height: 2px; background: var(--border-color); z-index: 1;"></div>
                
                @php
                    $stages = ['pending' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
                    $currentStage = $stages[$order->status] ?? 0;
                    $steps = [
                        ['label' => 'Order Received', 'icon' => '📜'],
                        ['label' => 'Atelier Prep', 'icon' => '🧵'],
                        ['label' => 'In Transit', 'icon' => '✈️'],
                        ['label' => 'Arrival', 'icon' => '🎁']
                    ];
                @endphp

                @foreach($steps as $index => $step)
                <div style="text-align: center; position: relative; z-index: 2;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $currentStage > $index ? 'var(--zyra-gold)' : 'var(--zyra-black)' }}; border: 3px solid {{ $currentStage > $index ? 'var(--zyra-gold)' : 'var(--border-color)' }}; margin: 0 auto 1.5rem; transition: all 0.5s ease; display: flex; align-items: center; justify-content: center;">
                        @if($currentStage > $index)
                            <span style="color: white; font-size: 0.8rem;">✓</span>
                        @endif
                    </div>
                    <span style="font-size: 1.2rem; display: block; margin-bottom: 0.5rem;">{{ $step['icon'] }}</span>
                    <h4 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: {{ $currentStage > $index ? 'var(--zyra-white)' : 'var(--zyra-silver)' }}; font-weight: 800;">{{ $step['label'] }}</h4>
                </div>
                @endforeach
            </div>

            <!-- Itemized List -->
            <div style="margin-top: 4rem;">
                <h3 style="font-size: 1.25rem; font-family: 'Syne', sans-serif; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Order Manifest</h3>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($order->orderItems as $item)
                    <div style="display: flex; align-items: center; gap: 2rem; padding: 1.5rem; background: var(--zyra-gray); border-radius: 8px;">
                        <img src="{{ Storage::url($item->product->main_image) }}" style="width: 80px; height: 100px; object-fit: cover; border-radius: 4px;">
                        <div style="flex: 1;">
                            <h4 style="font-size: 1.1rem; font-family: 'Syne', sans-serif;">{{ $item->product->name }}</h4>
                            <p style="font-size: 0.8rem; color: var(--zyra-silver); text-transform: uppercase; letter-spacing: 0.1em;">Size: {{ $item->size }} | Qty: {{ $item->quantity }}</p>
                        </div>
                        <span style="font-weight: 700; color: var(--zyra-gold);">&#8377;{{ number_format($item->total_price, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Totals -->
            <div style="margin-top: 3rem; display: flex; justify-content: flex-end;">
                <div style="width: 300px; display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                        <span style="color: var(--zyra-silver);">Subtotal</span>
                        <span style="font-weight: 600;">&#8377;{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--zyra-silver);">Atelier Fulfillment</span>
                        <span style="font-weight: 600;">&#8377;{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-family: 'Syne', sans-serif; padding-top: 1rem;">
                        <span style="color: var(--zyra-gold);">Total Amount</span>
                        <span style="font-weight: 800; color: var(--zyra-gold);">&#8377;{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
