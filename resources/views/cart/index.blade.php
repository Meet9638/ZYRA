@extends('layouts.app')

@section('title', 'Shopping Cart - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <h1   class="auto-style-0136">
        Shopping Cart
    </h1>

    @if(empty($items))
        <div   class="auto-style-0137">
            <div   class="auto-style-0138">??</div>
            <h2   class="auto-style-0139">Your cart is empty</h2>
            <p   class="auto-style-0140">Start shopping to add items to your cart</p>
            <a href="{{ route('shop') }}"   class="auto-style-0141">
                Browse Products
            </a>
        </div>
    @else
        <div   class="auto-style-0024">
            <!-- Cart Items -->
            <div>
                @foreach($items as $item)
                    <div   class="auto-style-0142">
                        <!-- Product Image -->
                        <div   class="auto-style-0143">
                            <img src="{{ $item['product']->main_image_url }}" alt="{{ $item['product']->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                        </div>

                        <!-- Product Details -->
                        <div   class="auto-style-0145">
                            <h3   class="auto-style-0146">
                                <a href="{{ route('products.show', $item['product']->slug) }}" style="color: var(--zyra-white); text-decoration: none;">
                                    {{ $item['product']->name }}
                                </a>
                            </h3>
                            <div   class="auto-style-0148">
                                Size: <strong   class="auto-style-0149">{{ $item['size'] }}</strong>
                            </div>

                            <!-- Price Display -->
                            <div   class="auto-style-0148" style="margin-top: 0.5rem;">
                                @if($item['product']->is_effectively_on_sale)
                                    <div>
                                        <span style="color: var(--zyra-rose); font-weight: 700;">&#8377;{{ number_format($item['unit_price'], 2) }}</span>
                                        <span style="font-size: 0.8rem; color: var(--zyra-silver); text-decoration: line-through; margin-left: 0.5rem;">&#8377;{{ number_format($item['product']->price, 2) }}</span>
                                    </div>
                                @else
                                    <div>&#8377;{{ number_format($item['unit_price'], 2) }} each</div>
                                @endif
                                <div style="font-size: 0.7rem; color: var(--zyra-silver); margin-top: 0.25rem;">* Price excludes taxes</div>
                            </div>

                            <div   class="auto-style-0152">
                                <!-- Quantity -->
                                <div   class="auto-style-0153">
                                    <button onclick="updateQuantity('{{ $item['key'] }}', {{ $item['quantity'] - 1 }})" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}   class="auto-style-0154">-</button>
                                    <span   class="auto-style-0155">{{ $item['quantity'] }}</span>
                                    <button onclick="updateQuantity('{{ $item['key'] }}', {{ $item['quantity'] + 1 }})"   class="auto-style-0154">+</button>
                                </div>

                                <!-- Price -->
                                <div   class="auto-style-0051">
                                    &#8377;{{ number_format($item['total'], 2) }}
                                </div>

                                <!-- Remove -->
                                <button onclick="removeItem('{{ $item['key'] }}')"   class="auto-style-0156">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div>
                <div   class="auto-style-0157">
                    <h3   class="auto-style-0158">
                        Order Summary
                    </h3>

                    <div   class="auto-style-0159">
                        <div   class="auto-style-0160">
                            <span>Subtotal:</span>
                            <span id="subtotal">&#8377;{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div   class="auto-style-0160">
                            <span>Tax (10%):</span>
                            <span id="tax">&#8377;{{ number_format($tax, 2) }}</span>
                        </div>
                        <div   class="auto-style-0160">
                            <span>Shipping:</span>
                            <span id="shipping">&#8377;{{ number_format($shippingCost, 2) }}</span>
                        </div>
                        @if($subtotal < 100)
                            <div   class="auto-style-0161">
                                Add &#8377;{{ number_format(100 - $subtotal, 2) }} more for free shipping!
                            </div>
                        @else
                            <div   class="auto-style-0162">
                                ? Free shipping applied!
                            </div>
                        @endif
                    </div>

                    <div   class="auto-style-0163">
                        <div   class="auto-style-0164">
                            <span>Total:</span>
                            <span id="total"   class="auto-style-0134">&#8377;{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('orders.checkout') }}"   class="auto-style-0165">
                        Proceed to Checkout
                    </a>

                    <a href="{{ route('shop') }}"   class="auto-style-0166">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function updateQuantity(cartKey, newQuantity) {
        if (newQuantity < 1) return;

        fetch(`/cart/${cartKey}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update quantity');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update quantity');
        });
    }

    function removeItem(cartKey) {
        if (!confirm('Remove this item from cart?')) return;

        fetch(`/cart/${cartKey}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to remove item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove item');
        });
    }
</script>
@endpush
@endsection
