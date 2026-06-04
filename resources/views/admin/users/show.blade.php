@extends('layouts.admin')

@section('page-title', 'User — ' . $user->name)

@section('content')



{{-- Back --}}
<a href="{{ route('admin.users.index') }}" class="back-link">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Users
</a>

@php
    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
    $colors   = ['#b45309','#0369a1','#7c3aed','#0f766e','#be185d','#15803d'];
    $color    = $colors[crc32($user->email) % count($colors)];
    $isAdmin  = ($user->role ?? '') === 'admin';
    $isBanned = isset($user->banned_at) && $user->banned_at;
    $verified = (bool) $user->email_verified_at;
@endphp

<div class="show-layout">

    {{-- ------- LEFT — Profile sidebar ------- --}}
    <div>

        {{-- Profile card --}}
        <div class="card">
            <div class="card-body auto-style-0117"  >
                <div class="profile-avatar" style="background:{{ $color }}1a;color:{{ $color }};">
                    {{ $initials }}
                </div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-email">{{ $user->email }}</div>
                <div class="profile-badges">
                    @if($isAdmin)
                        <span class="badge badge-admin">?? Admin</span>
                    @else
                        <span class="badge badge-user">?? User</span>
                    @endif

                    @if($isBanned)
                        <span class="badge badge-banned">?? Banned</span>
                    @else
                        <span class="badge badge-active">? Active</span>
                    @endif

                    @if($verified)
                        <span class="badge badge-verify">? Verified</span>
                    @else
                        <span class="badge badge-unver">? Unverified</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Account details --}}
        <div class="card">
            <div class="card-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a9 9 0 0 1 13 0"/></svg>
                Account Details
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-key">ID</span>
                    <span class="info-val auto-style-0118"  >#{{ $user->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Role</span>
                    <span class="info-val">{{ ucfirst($user->role ?? 'user') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Email Verified</span>
                    <span class="info-val">
                        @if($verified)
                            <span   class="auto-style-0119">{{ $user->email_verified_at->format('d M Y') }}</span>
                        @else
                            <span   class="auto-style-0111">Not verified</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-key">Joined</span>
                    <span class="info-val">{{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Last Updated</span>
                    <span class="info-val">{{ $user->updated_at->format('d M Y') }}</span>
                </div>
                @if($isBanned)
                <div class="info-row">
                    <span class="info-key">Banned At</span>
                    <span class="info-val auto-style-0120"  >{{ $user->banned_at->format('d M Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="danger-zone">
            <div>
                <strong>Delete Account</strong>
                <p>Permanently remove this user and all their data.</p>
            </div>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn-del">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6m4-6v6"/><path d="M9 6V4h6v2"/></svg>
                    Delete
                </button>
            </form>
        </div>

    </div>

    {{-- ------- RIGHT — Activity & orders ------- --}}
    <div>

        {{-- Quick stats --}}
        <div class="stat-boxes">
            <div class="stat-box">
                <div class="sv auto-style-0101"  >{{ $user->orders_count ?? 0 }}</div>
                <div class="sl">Total Orders</div>
            </div>
            <div class="stat-box">
                <div class="sv auto-style-0119"  >
                    &#8377;{{ number_format($user->orders()->sum('total_amount') ?? 0, 2) }}
                </div>
                <div class="sl">Total Spent</div>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="card">
            <div class="card-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Recent Orders
            </div>

            @php $orders = $user->orders()->latest()->take(8)->get(); @endphp

            @if($orders->isEmpty())
                <div class="empty-orders">
                    <div class="ei">??</div>
                    <p>No orders placed yet</p>
                </div>
            @else
                <table   class="auto-style-0121">
                    <thead>
                        <tr   class="auto-style-0122">
                            <th   class="auto-style-0123">Order</th>
                            <th   class="auto-style-0123">Date</th>
                            <th   class="auto-style-0123">Status</th>
                            <th   class="auto-style-0124">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $statusColors = [
                                'pending'    => '#fb923c',
                                'processing' => '#60a5fa',
                                'shipped'    => '#a78bfa',
                                'delivered'  => '#4ade80',
                                'cancelled'  => '#f87171',
                            ];
                            $sc = $statusColors[$order->status ?? 'pending'] ?? '#94a3b8';
                        @endphp
                        <tr  
                            onmouseover="this.style.background='rgba(212,175,55,.04)'"
                            onmouseout="this.style.background=''" class="auto-style-0125">
                            <td   class="auto-style-0126">
                                #{{ $order->order_number ?? $order->id }}
                            </td>
                            <td   class="auto-style-0127">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td   class="auto-style-0128">
                                <span style="display:inline-block;padding:.2rem .65rem;border-radius:20px;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:{{ $sc }};background:{{ $sc }}1a;border:1px solid {{ $sc }}44;">
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                            </td>
                            <td   class="auto-style-0129">
                                &#8377;{{ number_format($order->total_amount ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>

@endsection
