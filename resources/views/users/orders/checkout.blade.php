@extends('layouts.app')

@section('title', 'Checkout - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <h1   class="auto-style-0136">
        Checkout
    </h1>

    <div   class="auto-style-0211">
        <!-- Checkout Form -->
        <div>
            <form action="{{ route('orders.store') }}" method="POST" id="checkoutForm">
                @csrf

                <!-- Hidden items data -->
                @foreach($items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product']->id }}">
                    <input type="hidden" name="items[{{ $index }}][size]" value="{{ $item['size'] }}">
                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                @endforeach

                <!-- Contact Information -->
                <div   class="auto-style-0340">
                    <h3   class="auto-style-0341">
                        Contact Information
                    </h3>

                    <div   class="auto-style-0159">
                        <label   class="auto-style-0342">
                            Phone Number *
                        </label>
                        <input 
                            type="tel" 
                            name="phone" 
                            class="form-control @error('phone') is-invalid @enderror" 
                            value="{{ old('phone', auth()->user()->phone ?? '') }}" 
                            required
                            placeholder="+1 (555) 000-0000"
                            style="width: 100%; padding: 1rem; background: var(--zyra-gray); border: 1px solid rgba(212, 175, 55, 0.2); color: var(--zyra-white); border-radius: 4px;"
                        >
                        @error('phone')
                            <div   class="auto-style-0293">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Shipping Address -->
                <div   class="auto-style-0340">
                    <h3   class="auto-style-0341">
                        Shipping Address
                    </h3>

                    <div   class="auto-style-0159">
                        <label   class="auto-style-0342">
                            Full Address *
                        </label>
                        <textarea 
                            name="shipping_address" 
                            class="form-control @error('shipping_address') is-invalid @enderror auto-style-0344" 
                            required
                            rows="4"
                            placeholder="Street Address, City, State, ZIP Code, Country"
                             
                        >{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <div   class="auto-style-0293">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Billing Address -->
                <div   class="auto-style-0340">
                    <h3   class="auto-style-0341">
                        Billing Address
                    </h3>

                    <div   class="auto-style-0159">
                        <label   class="auto-style-0345">
                            <input type="checkbox" id="sameAsShipping" onchange="toggleBillingAddress()">
                            <span   class="auto-style-0043">Same as shipping address</span>
                        </label>
                    </div>

                    <div id="billingAddressFields">
                        <div   class="auto-style-0159">
                            <label   class="auto-style-0342">
                                Full Address *
                            </label>
                            <textarea 
                                name="billing_address" 
                                id="billingAddress"
                                class="form-control @error('billing_address') is-invalid @enderror auto-style-0344" 
                                required
                                rows="4"
                                placeholder="Street Address, City, State, ZIP Code, Country"
                                 
                            >{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <div   class="auto-style-0293">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div   class="auto-style-0340">
                    <h3   class="auto-style-0341">
                        Order Notes (Optional)
                    </h3>

                    <div   class="auto-style-0159">
                        <textarea 
                            name="notes" 
                            class="form-control auto-style-0344" 
                            rows="4"
                            placeholder="Any special instructions for your order?"
                             
                        >{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Place Order Button -->
                <button type="submit"   class="auto-style-0346">
                    Place Order • &#8377;{{ number_format($total, 2) }}
                </button>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div>
            <div   class="auto-style-0157">
                <h3   class="auto-style-0158">
                    Order Summary
                </h3>

                <!-- Order Items -->
                <div   class="auto-style-0260">
                    @foreach($items as $item)
                        <div   class="auto-style-0347">
                            <div   class="auto-style-0348">
                                <img src="{{ Storage::url($item['product']->main_image) }}" alt="{{ $item['product']->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div   class="auto-style-0145">
                                <div   class="auto-style-0048">{{ $item['product']->name }}</div>
                                <div   class="auto-style-0349">
                                    Size: {{ $item['size'] }} • Qty: {{ $item['quantity'] }}
                                </div>
                                <div   class="auto-style-0193">
                                    &#8377;{{ number_format($item['total'], 2) }}
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div   class="auto-style-0159">
                    <div   class="auto-style-0160">
                        <span>Subtotal:</span>
                        <span>&#8377;{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div   class="auto-style-0160">
                        <span>Tax (10%):</span>
                        <span>&#8377;{{ number_format($tax, 2) }}</span>
                    </div>
                    <div   class="auto-style-0160">
                        <span>Shipping:</span>
                        <span>&#8377;{{ number_format($shippingCost, 2) }}</span>
                    </div>
                    @if($shippingCost == 0)
                        <div   class="auto-style-0162">
                            ✨ Free shipping applied!
                        </div>
                    @endif
                </div>

                <div   class="auto-style-0351">
                    <div   class="auto-style-0164">
                        <span>Total:</span>
                        <span class="auto-style-0134">&#8377;{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <!-- Security Badge -->
                <div   class="auto-style-0352">
                    🔒 Secure Checkout
                    <div   class="auto-style-0353">
                        Your payment information is encrypted
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleBillingAddress() {
        const checkbox = document.getElementById('sameAsShipping');
        const billingFields = document.getElementById('billingAddressFields');
        const billingAddress = document.getElementById('billingAddress');
        const shippingAddress = document.querySelector('textarea[name="shipping_address"]');

        if (checkbox.checked) {
            billingAddress.value = shippingAddress.value;
            billingFields.style.opacity = '0.5';
            billingAddress.readOnly = true;
        } else {
            billingAddress.readOnly = false;
            billingFields.style.opacity = '1';
        }
    }

    // Auto-copy shipping to billing when shipping changes (if checkbox is checked)
    document.querySelector('textarea[name="shipping_address"]').addEventListener('input', function() {
        const checkbox = document.getElementById('sameAsShipping');
        if (checkbox.checked) {
            document.getElementById('billingAddress').value = this.value;
        }
    });

    // Form validation and submission
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const phone = document.querySelector('input[name="phone"]').value;
        const shipping = document.querySelector('textarea[name="shipping_address"]').value;
        const billing = document.querySelector('textarea[name="billing_address"]').value;

        if (!phone || !shipping || !billing) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing Order...';
    });
</script>
@endpush
@endsection

