@extends('layouts.app')

@section('title', 'Return Details - ZYRA')

@section('content')
<div class="container" style="padding: 120px 0 80px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem;">
            <div>
                <a href="{{ route('returns.index') }}" style="color: var(--zyra-gold); text-decoration: none; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.2em; display: block; margin-bottom: 1.5rem;">← Back to My Returns</a>
                <h1 style="font-size: 3rem; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">Return <span class="text-gradient">Details</span></h1>
                <p style="color: var(--zyra-silver);">Return Number: <span style="color: white; font-weight: 700;">{{ $return->return_number }}</span></p>
            </div>
            <div>
                @php
                    $statusColors = [
                        'requested' => ['bg' => 'rgba(232, 111, 28, 0.1)', 'text' => 'var(--zyra-gold)'],
                        'approved' => ['bg' => 'rgba(0, 123, 255, 0.1)', 'text' => '#007bff'],
                        'rejected' => ['bg' => 'rgba(220, 53, 69, 0.1)', 'text' => '#dc3545'],
                        'completed' => ['bg' => 'rgba(40, 167, 69, 0.1)', 'text' => '#28a745'],
                    ];
                    $colors = $statusColors[$return->status] ?? ['bg' => 'rgba(255,255,255,0.05)', 'text' => 'var(--zyra-silver)'];
                @endphp
                <span style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 0.8rem 2rem; border-radius: 4px; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em;">
                    Status: {{ $return->status }}
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem;">
            <div>
                <div class="glass-card" style="padding: 3rem; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; font-family: 'Syne', sans-serif; margin-bottom: 2rem; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.1em;">Return Information</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div>
                            <span style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); margin-bottom: 0.5rem;">Reason</span>
                            <p style="font-size: 1.1rem; font-weight: 500;">{{ ucfirst(str_replace('_', ' ', $return->reason)) }}</p>
                        </div>

                        @if($return->detailed_reason)
                            <div>
                                <span style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); margin-bottom: 0.5rem;">Detailed Reason</span>
                                <p style="line-height: 1.6; color: rgba(255,255,255,0.8);">{{ $return->detailed_reason }}</p>
                            </div>
                        @endif

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <div>
                                <span style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); margin-bottom: 0.5rem;">Date Requested</span>
                                <p>{{ $return->created_at->format('M d, Y') }}</p>
                            </div>
                            @if($return->completed_at)
                                <div>
                                    <span style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-silver); margin-bottom: 0.5rem;">Date Completed</span>
                                    <p>{{ $return->completed_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($return->admin_notes)
                    <div class="glass-card" style="padding: 2rem; border-left: 4px solid var(--zyra-gold);">
                        <h4 style="font-size: 0.9rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--zyra-gold);">Merchant Notes</h4>
                        <p style="line-height: 1.6;">{{ $return->admin_notes }}</p>
                    </div>
                @endif
            </div>

            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="glass-card" style="padding: 2.5rem;">
                    <h3 style="font-size: 1.1rem; font-family: 'Syne', sans-serif; margin-bottom: 2rem; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.1em;">Product Info</h3>
                    <div style="display: flex; gap: 1.5rem; mb-2;">
                        @if($product = $return->orderItem->product)
                            <div style="width: 80px; height: 100px; background: var(--zyra-gray); border-radius: 4px; overflow: hidden; flex-shrink: 0;">
                                @if($product->main_image)
                                    <img src="{{ Str::startsWith($product->main_image, 'http') ? $product->main_image : asset('storage/' . $product->main_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @endif
                            </div>
                            <div>
                                <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">{{ $product->name }}</h4>
                                <div style="display: flex; flex-direction: column; gap: 0.3rem; font-size: 0.85rem; color: var(--zyra-silver);">
                                    <span>Size Returned: {{ $return->returned_size }}</span>
                                    @if($return->preferred_replacement_size)
                                        <span style="color: var(--zyra-gold);">Requested Size: {{ $return->preferred_replacement_size }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="glass-card" style="padding: 2.5rem;">
                    <h3 style="font-size: 1.1rem; font-family: 'Syne', sans-serif; margin-bottom: 2rem; color: var(--zyra-gold); text-transform: uppercase; letter-spacing: 0.1em;">Need Help?</h3>
                    <p style="font-size: 0.9rem; color: var(--zyra-silver); line-height: 1.6; margin-bottom: 1.5rem;">If you have any questions regarding your return, please contact our concierge team.</p>
                    <a href="{{ route('contact') }}" class="btn-premium" style="width: 100%; text-align: center; display: block; padding: 1rem;">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
