@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <a href="{{ route('orders.index') }}"   class="auto-style-0210">
        ? Back to Orders
    </a>

    <div   class="auto-style-0211">
        <!-- Order Details -->
        <div>
            <div   class="auto-style-0212">
                <div   class="auto-style-0213">
                    <div>
                        <h1   class="auto-style-0214">Order {{ $order->order_number }}</h1>
                        <div   class="auto-style-0043">
                            Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                        </div>
                    </div>
                    <div   class="auto-style-0050">
                        <div   class="auto-style-0215">
                            {{ ucfirst($order->status) }}
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <h3   class="auto-style-0216">Order Items</h3>
                @foreach($order->orderItems as $item)
                    <div   class="auto-style-0217">
                        <div   class="auto-style-0218">
                            <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div   class="auto-style-0145">
                            <h4   class="auto-style-0219">{{ $item->product->name }}</h4>
                            <div   class="auto-style-0220">
                                Size: <strong>{{ $item->size }}</strong> • Quantity: {{ $item->quantity }}
                            </div>
                            @if($item->ai_recommended_size)
                                <div style="margin-bottom: 0.8rem; padding: 0.6rem; font-size: 0.9rem;
                                    {{ $item->followed_ai_recommendation 
                                        ? 'background: rgba(74, 222, 128, 0.1); border-left: 3px solid var(--success); color: var(--success);' 
                                        : 'background: rgba(251, 191, 36, 0.1); border-left: 3px solid var(--warning); color: var(--warning);' }}">
                                    @if($item->followed_ai_recommendation)
                                        ? You followed AI recommendation ({{ $item->ai_confidence }}% confidence)
                                    @else
                                        ?? AI suggested size {{ $item->ai_recommended_size }} ({{ $item->ai_confidence }}% confidence)
                                    @endif
                                </div>
                            @endif
                            <div   class="auto-style-0023">
                                &#8377;{{ number_format($item->total_price, 2) }}
                            </div>
                        </div>
                        @if($order->status === 'delivered' && !$item->return)
                            <div>
                                <a href="{{ route('returns.create', $item) }}"   class="auto-style-0221">
                                    Request Return
                                </a>
                            </div>
                        @elseif($item->return)
                            <div>
                                <a href="{{ route('returns.show', $item->return) }}" style="padding: 0.8rem 1.5rem; background: rgba(248, 113, 113, 0.2); border: 1px solid var(--danger); color: var(--danger); text-decoration: none; font-weight: 600; border-radius: 4px; display: inline-block;">
                                    View Return
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Shipping Information -->
            <div   class="auto-style-0223">
                <h3   class="auto-style-0216">Shipping Information</h3>
                <div   class="auto-style-0224">
                    <div>
                        <h4   class="auto-style-0225">Shipping Address</h4>
                        <div   class="auto-style-0226">{{ $order->shipping_address }}</div>
                    </div>
                    <div>
                        <h4   class="auto-style-0225">Billing Address</h4>
                        <div   class="auto-style-0226">{{ $order->billing_address }}</div>
                    </div>
                </div>
                <div   class="auto-style-0227">
                    <h4   class="auto-style-0225">Contact</h4>
                    <div   class="auto-style-0149">{{ $order->phone }}</div>
                </div>
                @if($order->notes)
                    <div   class="auto-style-0227">
                        <h4   class="auto-style-0225">Order Notes</h4>
                        <div   class="auto-style-0228">{{ $order->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div>
            <div   class="auto-style-0157">
                <h3   class="auto-style-0229">
                    Order Summary
                </h3>

                <div   class="auto-style-0159">
                    <div   class="auto-style-0160">
                        <span>Subtotal:</span>
                        <span>&#8377;{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div   class="auto-style-0160">
                        <span>Tax:</span>
                        <span>&#8377;{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div   class="auto-style-0160">
                        <span>Shipping:</span>
                        <span>&#8377;{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                </div>

                <div   class="auto-style-0163">
                    <div   class="auto-style-0164">
                        <span>Total:</span>
                        <span   class="auto-style-0134">&#8377;{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                @if(in_array($order->status, ['pending', 'processing']))
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');"   class="auto-style-0230">
                        @csrf
                        <button type="submit"   class="auto-style-0231">
                            Cancel Order
                        </button>
                    </form>
                @endif

                <!-- Tracking Info -->
                @if($order->shipped_at)
                    <div   class="auto-style-0232">
                        <h4   class="auto-style-0233">Shipping Status</h4>
                        <div   class="auto-style-0234">
                            <strong>Shipped:</strong> {{ $order->shipped_at->format('M d, Y') }}
                        </div>
                        @if($order->delivered_at)
                            <div   class="auto-style-0235">
                                <strong>Delivered:</strong> {{ $order->delivered_at->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

