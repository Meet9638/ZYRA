@extends('layouts.app')

@section('title', 'Request Return - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <a href="{{ route('orders.show', $orderItem->order) }}" style="color: var(--zyra-gold); text-decoration: none; margin-bottom: 2rem; display: inline-block;">
        ? Back to Order
    </a>

    <h1   class="auto-style-0286">
        Request Return
    </h1>

    <div   class="auto-style-0287">
        <!-- Return Form -->
        <div>
            <div   class="auto-style-0212">
                <!-- Product Info -->
                <div   class="auto-style-0288">
                    <div   class="auto-style-0289">
                        <img src="{{ Storage::url($orderItem->product->main_image) }}" alt="{{ $orderItem->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <h3   class="auto-style-0146">{{ $orderItem->product->name }}</h3>
                        <div   class="auto-style-0290">
                            Size: <strong>{{ $orderItem->size }}</strong> • Quantity: {{ $orderItem->quantity }}
                        </div>
                        <div   class="auto-style-0193">
                            &#8377;{{ number_format($orderItem->total_price, 2) }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('returns.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $orderItem->id }}">

                    <!-- Return Reason -->
                    <div   class="auto-style-0260">
                        <label   class="auto-style-0291">
                            Reason for Return *
                        </label>
                        <select name="reason" required   class="auto-style-0292">
                            <option value="">Select a reason</option>
                            <option value="too_small">Size too small</option>
                            <option value="too_large">Size too large</option>
                            <option value="wrong_item">Wrong item received</option>
                            <option value="damaged">Item damaged</option>
                            <option value="quality_issue">Quality issue</option>
                            <option value="not_as_described">Not as described</option>
                            <option value="other">Other</option>
                        </select>
                        @error('reason')
                            <div   class="auto-style-0293">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Detailed Reason -->
                    <div   class="auto-style-0260">
                        <label   class="auto-style-0291">
                            Additional Details
                        </label>
                        <textarea name="detailed_reason" rows="4" placeholder="Please provide more details about why you're returning this item..."   class="auto-style-0294">{{ old('detailed_reason') }}</textarea>
                        @error('detailed_reason')
                            <div   class="auto-style-0293">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Preferred Replacement Size -->
                    <div   class="auto-style-0260">
                        <label   class="auto-style-0291">
                            Preferred Replacement Size (Optional)
                        </label>
                        <div   class="auto-style-0262">
                            @foreach($orderItem->product->productSizes as $size)
                                <label   class="auto-style-0263">
                                    <input type="radio" name="preferred_replacement_size" value="{{ $size->size }}" style="position: absolute; opacity: 0;">
                                    <div   
                                         onclick="document.querySelectorAll('[name=preferred_replacement_size]').forEach(r = class="auto-style-0295"> r.parentElement.querySelector('div').style.borderColor='var(--zyra-silver)'); this.querySelector('div') ? this.querySelector('div').style.borderColor='var(--zyra-gold)' : null;">
                                        {{ $size->size }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div   class="auto-style-0296">
                            Leave blank if you prefer a refund
                        </div>
                    </div>

                    <button type="submit"   class="auto-style-0297">
                        Submit Return Request
                    </button>
                </form>
            </div>
        </div>

        <!-- Return Policy Sidebar -->
        <div>
            <div   class="auto-style-0157">
                <h3   class="auto-style-0298">Return Policy</h3>
                
                <div   class="auto-style-0159">
                    <h4   class="auto-style-0233">? 30-Day Returns</h4>
                    <p   class="auto-style-0299">
                        Items can be returned within 30 days of delivery
                    </p>
                </div>

                <div   class="auto-style-0159">
                    <h4   class="auto-style-0233">? Free Returns</h4>
                    <p   class="auto-style-0299">
                        We cover return shipping costs
                    </p>
                </div>

                <div   class="auto-style-0159">
                    <h4   class="auto-style-0233">? AI Learning</h4>
                    <p   class="auto-style-0299">
                        Your return helps improve our AI recommendations
                    </p>
                </div>

                <div   class="auto-style-0300">
                    ?? <strong>Tip:</strong> Our AI will learn from this return to provide better size recommendations for your future purchases
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

