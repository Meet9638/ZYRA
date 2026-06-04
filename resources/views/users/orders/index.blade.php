@extends('layouts.app')

@section('title', 'My Orders - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <div   class="auto-style-0354">
        <h1   class="auto-style-0355">
            My Orders
        </h1>
        <a href="{{ route('shop') }}"   class="auto-style-0356">
            Continue Shopping
        </a>
    </div>

    @if($orders->count() > 0)
        <div   class="auto-style-0301">
            @foreach($orders as $order)
                <div   class="auto-style-0302">
                    <!-- Order Header -->
                    <div   class="auto-style-0303">
                        <div>
                            <h3   class="auto-style-0146">Order {{ $order->order_number }}</h3>
                            <div   class="auto-style-0049">
                                Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                            </div>
                        </div>
                        <div   class="auto-style-0050">
                            <div   class="auto-style-0357">
                                &#8377;{{ number_format($order->total_amount, 2) }}
                            </div>
                            <div   class="auto-style-0358">
                                {{ ucfirst($order->status) }}
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div   class="auto-style-0359">
                        @foreach($order->orderItems as $item)
                            <div   class="auto-style-0360">
                                <div   class="auto-style-0307">
                                    <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div   class="auto-style-0145">
                                    <h4   class="auto-style-0284">{{ $item->product->name }}</h4>
                                    <div   class="auto-style-0361">
                                        Size: <strong>{{ $item->size }}</strong> • Quantity: {{ $item->quantity }}
                                    </div>
                                    @if($item->ai_recommended_size)
                                        <div   class="auto-style-0362">
                                            @if($item->followed_ai_recommendation)
                                                <span   class="auto-style-0235">? You followed AI recommendation ({{ $item->ai_confidence }}% confidence)</span>
                                            @else
                                                <span   class="auto-style-0363">?? AI suggested size {{ $item->ai_recommended_size }} ({{ $item->ai_confidence }}% confidence)</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div   class="auto-style-0193">
                                        &#8377;{{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                                @if($order->status === 'delivered' && !$item->return)
                                    <div>
                                        <a href="{{ route('returns.create', $item) }}"   class="auto-style-0364">
                                            Request Return
                                        </a>
                                    </div>
                                @elseif($item->return)
                                    <div>
                                        <a href="{{ route('returns.show', $item->return) }}" style="padding: 0.6rem 1.2rem; background: rgba(248, 113, 113, 0.2); border: 1px solid var(--danger); color: var(--danger); text-decoration: none; font-size: 0.85rem; border-radius: 4px; display: inline-block;">
                                            View Return
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Actions -->
                    <div   class="auto-style-0366">
                        <div   class="auto-style-0049">
                            {{ $order->orderItems->count() }} item(s) in this order
                        </div>
                        <div   class="auto-style-0171">
                            <a href="{{ route('orders.show', $order) }}"   class="auto-style-0367">
                                View Details
                            </a>
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                    @csrf
                                    <button type="submit"   class="auto-style-0368">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div   class="auto-style-0314">
                {{ $orders->links() }}
            </div>
        @endif

    @else
        <div   class="auto-style-0315">
            <div   class="auto-style-0316">??</div>
            <h2   class="auto-style-0317">No Orders Yet</h2>
            <p   class="auto-style-0318">
                You haven't placed any orders yet. Start shopping to see your orders here!
            </p>
            <a href="{{ route('shop') }}"   class="auto-style-0369">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection

