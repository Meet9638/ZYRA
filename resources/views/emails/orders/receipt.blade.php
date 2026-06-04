<x-mail::message>
# Order Receipt

Thank you for your purchase from ZYRA!

Your order **{{ $order->order_number }}** has been placed successfully and is currently being processed.

<x-mail::table>
| Product | Size | Quantity | Price | Total |
|:---|:---:|:---:|:---:|---:|
@foreach($order->orderItems as $item)
| {{ $item->product->name }} | {{ $item->size }} | {{ $item->quantity }} | ₹{{ number_format($item->unit_price, 2) }} | ₹{{ number_format($item->total_price, 2) }} |
@endforeach
</x-mail::table>

<x-mail::panel>
**Subtotal:** ₹{{ number_format($order->subtotal, 2) }}  
**Tax (10%):** ₹{{ number_format($order->tax, 2) }}  
**Shipping:** ₹{{ number_format($order->shipping_cost, 2) }}  

**Total Amount:** ₹{{ number_format($order->total_amount, 2) }}
</x-mail::panel>

**Shipping Address:**  
{{ $order->shipping_address }}

<x-mail::button :url="route('orders.show', $order)">
View Order Details
</x-mail::button>

Thanks,<br>
NOIR TEAM
</x-mail::message>

