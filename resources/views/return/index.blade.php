@extends('layouts.app')

@section('title', 'My Returns - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <h1   class="auto-style-0286">
        My Returns
    </h1>

    @if($returns->count() > 0)
        <div   class="auto-style-0301">
            @foreach($returns as $return)
                <div   class="auto-style-0302">
                    <!-- Return Header -->
                    <div   class="auto-style-0303">
                        <div>
                            <h3   class="auto-style-0146">Return {{ $return->return_number }}</h3>
                            <div   class="auto-style-0049">
                                Requested on {{ $return->created_at->format('F d, Y') }}
                            </div>
                            <div   class="auto-style-0049">
                                Order: <a href="{{ route('orders.show', $return->orderItem->order) }}" style="color: var(--zyra-gold); text-decoration: none;">{{ $return->orderItem->order->order_number }}</a>
                            </div>
                        </div>
                        <div   class="auto-style-0050">
                            <div   class="auto-style-0305">
                                {{ ucfirst($return->status) }}
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div   class="auto-style-0306">
                        <div   class="auto-style-0307">
                            <img src="{{ Storage::url($return->orderItem->product->main_image) }}" alt="{{ $return->orderItem->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div   class="auto-style-0145">
                            <h4   class="auto-style-0284">{{ $return->orderItem->product->name }}</h4>
                            <div   class="auto-style-0290">
                                Returned Size: <strong>{{ $return->returned_size }}</strong>
                            </div>
                            <div   class="auto-style-0290">
                                Reason: <strong>{{ ucfirst(str_replace('_', ' ', $return->reason)) }}</strong>
                            </div>
                            @if($return->preferred_replacement_size)
                                <div   class="auto-style-0134">
                                    Preferred Replacement: <strong>Size {{ $return->preferred_replacement_size }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($return->detailed_reason)
                        <div   class="auto-style-0308">
                            <div   class="auto-style-0309">Details</div>
                            <div   class="auto-style-0149">{{ $return->detailed_reason }}</div>
                        </div>
                    @endif

                    @if($return->admin_notes)
                        <div   class="auto-style-0310">
                            <div   class="auto-style-0311">Admin Response</div>
                            <div   class="auto-style-0149">{{ $return->admin_notes }}</div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div   class="auto-style-0312">
                        <a href="{{ route('returns.show', $return) }}"   class="auto-style-0313">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($returns->hasPages())
            <div   class="auto-style-0314">
                {{ $returns->links() }}
            </div>
        @endif

    @else
        <div   class="auto-style-0315">
            <div   class="auto-style-0316">↩️</div>
            <h2   class="auto-style-0317">No Returns</h2>
            <p   class="auto-style-0318">
                You haven't requested any returns yet
            </p>
            <a href="{{ route('orders.index') }}"   class="auto-style-0319">
                View Orders
            </a>
        </div>
    @endif
</div>
@endsection

