@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Complete Your Payment</h1>
            
            <form id="paymentForm" action="{{ route('payment.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Full Name
                    </label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="John Doe">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="john@example.com">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                        Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="+91 9876543210">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">
                        Amount (₹)
                    </label>
                    <input type="number" id="amount" name="amount" required step="0.01" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="100.00" value="{{ $amount ?? '' }}">
                </div>
                
                <button type="submit" id="payButton" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                    Pay Now
                </button>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('paymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const payButton = document.getElementById('payButton');
    
    payButton.disabled = true;
    payButton.textContent = 'Processing...';
    
    try {
        const response = await fetch('{{ route("payment.create") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                amount: formData.get('amount'),
            }),
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Payment initialization failed');
        }
        
        const options = {
            key: data.key,
            amount: data.amount,
            currency: data.currency,
            name: data.name,
            email: data.email,
            contact: data.phone,
            order_id: data.order_id,
            handler: async function(response) {
                try {
                    const verifyResponse = await fetch('{{ route("payment.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                        }),
                    });
                    
                    const verifyData = await verifyResponse.json();
                    
                    if (verifyData.success) {
                        window.location.href = '{{ route("payment.success") }}';
                    } else {
                        window.location.href = '{{ route("payment.failure") }}';
                    }
                } catch (error) {
                    console.error('Payment verification failed:', error);
                    window.location.href = '{{ route("payment.failure") }}';
                }
            },
            modal: {
                ondismiss: function() {
                    payButton.disabled = false;
                    payButton.textContent = 'Pay Now';
                }
            },
            theme: {
                color: '#2563eb'
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
        
    } catch (error) {
        console.error('Payment error:', error);
        alert('Payment failed: ' + error.message);
        payButton.disabled = false;
        payButton.textContent = 'Pay Now';
    }
});
</script>
@endsection
