@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="w-16 h-16 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Payment Failed</h1>
            
            <p class="text-gray-600 mb-6">
                We're sorry, but your payment could not be processed. Please try again or contact support if the problem persists.
            </p>
            
            <div class="space-y-3">
                <a href="{{ route('cart.index') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                    Try Again
                </a>
                
                <a href="{{ route('home') }}" class="block w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
