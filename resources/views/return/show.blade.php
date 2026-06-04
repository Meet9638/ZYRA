@extends('layouts.app')

@section('title', 'Return #' . $return->return_number . ' - ZYRA')

@section('content')
<div class="container auto-style-0135"  >
    <a href="{{ route('returns.index') }}"   class="auto-style-0210">
        ? Back to Returns
    </a>

    <div   class="auto-style-0287">
        <!-- Return Details -->
        <div>
            <div   class="auto-style-0212">
                <div   class="auto-style-0213">
                    <div>
                        <h1   class="auto-style-0214">Return {{ $return->return_number }}</h1>
                        <div   class="auto-style-0043">
                            Requested on {{ $return->created_at->format('F d, Y') }}
                        </div>
                        <div   class="auto-style-0320">
                            Order: <a href="{{ route('orders.show', $return->orderItem->order) }}" style="color: var(--zyra-gold); text-decoration: none;">{{ $return->orderItem->order->order_number }}</a>
                        </div>
                    </div>
                    <div   class="auto-style-0321">
                        {{ ucfirst($return->status) }}
                    </div>
                </div>

                <!-- Product Info -->
                <h3   class="auto-style-0216">Product Details</h3>
                <div   class="auto-style-0322">
                    <div   class="auto-style-0218">
                        <img src="{{ Storage::url($return->orderItem->product->main_image) }}" alt="{{ $return->orderItem->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div   class="auto-style-0145">
                        <h4   class="auto-style-0219">{{ $return->orderItem->product->name }}</h4>
                        <div   class="auto-style-0290">
                            Returned Size: <strong>{{ $return->returned_size }}</strong>
                        </div>
                        @if($return->preferred_replacement_size)
                            <div   class="auto-style-0323">
                                Preferred Replacement: <strong>Size {{ $return->preferred_replacement_size }}</strong>
                            </div>
                        @endif
                        <div   class="auto-style-0324">
                            &#8377;{{ number_format($return->orderItem->total_price, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Return Reason -->
                <h3   class="auto-style-0216">Return Reason</h3>
                <div   class="auto-style-0325">
                    <div   class="auto-style-0326">
                        {{ ucfirst(str_replace('_', ' ', $return->reason)) }}
                    </div>
                    @if($return->detailed_reason)
                        <div   class="auto-style-0228">
                            {{ $return->detailed_reason }}
                        </div>
                    @endif
                </div>

                <!-- Admin Response -->
                @if($return->admin_notes)
                    <h3   class="auto-style-0216">Admin Response</h3>
                    <div   class="auto-style-0327">
                        <div   class="auto-style-0228">
                            {{ $return->admin_notes }}
                        </div>
                        @if($return->updated_at != $return->created_at)
                            <div   class="auto-style-0328">
                                Updated: {{ $return->updated_at->format('F d, Y g:i A') }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Timeline -->
                <h3   class="auto-style-0216">Return Timeline</h3>
                <div   class="auto-style-0046">
                    <div   class="auto-style-0329">
                        <div   class="auto-style-0330">?</div>
                        <div   class="auto-style-0145">
                            <div   class="auto-style-0048">Return Requested</div>
                            <div   class="auto-style-0049">{{ $return->created_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>

                    @if($return->approved_at)
                        <div   class="auto-style-0329">
                            <div   class="auto-style-0330">?</div>
                            <div   class="auto-style-0145">
                                <div   class="auto-style-0048">Return Approved</div>
                                <div   class="auto-style-0049">{{ $return->approved_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                    @elseif($return->rejected_at)
                        <div   class="auto-style-0329">
                            <div   class="auto-style-0331">?</div>
                            <div   class="auto-style-0145">
                                <div   class="auto-style-0332">Return Rejected</div>
                                <div   class="auto-style-0049">{{ $return->rejected_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($return->completed_at)
                        <div   class="auto-style-0329">
                            <div   class="auto-style-0330">?</div>
                            <div   class="auto-style-0145">
                                <div   class="auto-style-0048">Return Completed</div>
                                <div   class="auto-style-0049">{{ $return->completed_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div   class="auto-style-0157">
                <h3   class="auto-style-0216">Return Summary</h3>

                <div   class="auto-style-0159">
                    <div   class="auto-style-0096">Status</div>
                    <div   class="auto-style-0333">{{ $return->status }}</div>
                </div>

                <div   class="auto-style-0334">
                    <div   class="auto-style-0096">Return Amount</div>
                    <div   class="auto-style-0335">
                        &#8377;{{ number_format($return->orderItem->total_price, 2) }}
                    </div>
                </div>

                @if($return->status === 'requested')
                    <div   class="auto-style-0336">
                        ? Your return request is being reviewed by our team
                    </div>
                @elseif($return->status === 'approved')
                    <div   class="auto-style-0337">
                        ? Your return has been approved! Please ship the item back to us.
                    </div>
                @elseif($return->status === 'completed')
                    <div   class="auto-style-0337">
                        ? Return completed! Your refund has been processed.
                    </div>
                @elseif($return->status === 'rejected')
                    <div   class="auto-style-0338">
                        ? Return request rejected. See admin notes for details.
                    </div>
                @endif

                <div   class="auto-style-0093">
                    <a href="{{ route('orders.show', $return->orderItem->order) }}" style="display: block; width: 100%; padding: 1rem; background: transparent; border: 1px solid var(--zyra-silver); color: var(--zyra-white); text-decoration: none; text-align: center; font-weight: 600; border-radius: 4px;">
                        View Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

