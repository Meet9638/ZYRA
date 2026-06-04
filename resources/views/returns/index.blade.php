@extends('layouts.app')

@section('title', 'My Returns - ZYRA')

@section('content')
<div class="container" style="padding: 120px 0 80px;">
    <div style="margin-bottom: 4rem;">
        <h1 style="font-size: 3rem; font-family: 'Syne', sans-serif; margin-bottom: 1rem;">My <span class="text-gradient">Returns</span></h1>
        <p style="color: var(--zyra-silver);">Track and manage your return requests.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(40, 167, 69, 0.1); border: 1px solid #28a745; color: #28a745; padding: 1rem; border-radius: 4px; margin-bottom: 2rem;">
            {{ session('success') }}
        </div>
    @endif

    @if($returns->isEmpty())
        <div class="glass-card" style="padding: 5rem; text-align: center;">
            <div style="font-size: 4rem; opacity: 0.2; margin-bottom: 2rem;">📦</div>
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">No returns found</h2>
            <p style="color: var(--zyra-silver); margin-bottom: 2rem;">You haven't initiated any return requests yet.</p>
            <a href="{{ route('users.orders') }}" class="btn-premium">View My Orders</a>
        </div>
    @else
        <div class="glass-card" style="overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <th style="padding: 1.5rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Return ID</th>
                            <th style="padding: 1.5rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Product</th>
                            <th style="padding: 1.5rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Date Requested</th>
                            <th style="padding: 1.5rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Status</th>
                            <th style="padding: 1.5rem; color: var(--zyra-gold); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returns as $return)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.5rem; font-weight: 700; font-family: 'Syne', sans-serif;">{{ $return->return_number }}</td>
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        @if($product = $return->orderItem->product)
                                            <div style="width: 50px; height: 50px; background: var(--zyra-gray); border-radius: 4px; overflow: hidden; flex-shrink: 0;">
                                                @if($product->main_image)
                                                    <img src="{{ Str::startsWith($product->main_image, 'http') ? $product->main_image : asset('storage/' . $product->main_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @endif
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">{{ $product->name }}</div>
                                                <div style="font-size: 0.8rem; color: var(--zyra-silver);">Size: {{ $return->returned_size }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1.5rem; color: var(--zyra-silver);">{{ $return->created_at->format('M d, Y') }}</td>
                                <td style="padding: 1.5rem;">
                                    @php
                                        $statusColors = [
                                            'requested' => ['bg' => 'rgba(232, 111, 28, 0.1)', 'text' => 'var(--zyra-gold)'],
                                            'approved' => ['bg' => 'rgba(0, 123, 255, 0.1)', 'text' => '#007bff'],
                                            'rejected' => ['bg' => 'rgba(220, 53, 69, 0.1)', 'text' => '#dc3545'],
                                            'completed' => ['bg' => 'rgba(40, 167, 69, 0.1)', 'text' => '#28a745'],
                                        ];
                                        $colors = $statusColors[$return->status] ?? ['bg' => 'rgba(255,255,255,0.05)', 'text' => 'var(--zyra-silver)'];
                                    @endphp
                                    <span style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">
                                        {{ $return->status }}
                                    </span>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <a href="{{ route('returns.show', $return) }}" style="color: var(--zyra-gold); text-decoration: none; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($returns->hasPages())
                <div style="padding: 2rem;">
                    {{ $returns->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
