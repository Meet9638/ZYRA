<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display cart
     */
    public function index()
    {
        $cartItems = session('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cartItems as $key => $item) {
            $product = Product::with('productSizes')->find($item['product_id']);
            
            if ($product) {
                $activePrice = $product->active_price;
                $itemTotal = $activePrice * $item['quantity'];
                $subtotal += $itemTotal;

                $items[] = [
                    'key' => $key,
                    'product' => $product,
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $activePrice,
                    'total' => $itemTotal,
                ];
            }
        }

        $tax = $subtotal * 0.1; // 10% tax
        $shippingCost = $subtotal > 100 ? 0 : 10; // Free shipping over $100
        $total = $subtotal + $tax + $shippingCost;

        return view('cart.index', compact('items', 'subtotal', 'tax', 'shippingCost', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add products to your cart.',
                'redirect' => route('login'),
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This product is no longer available.',
            ], 400);
        }

        // Check stock availability
        $productSize = $product->productSizes()
            ->where('size', $validated['size'])
            ->first();

        if (!$productSize || $productSize->stock_quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock for the selected size.',
            ], 400);
        }

        // Get cart from session
        $cart = session('cart', []);

        // Generate cart item key
        $cartKey = $validated['product_id'] . '_' . $validated['size'];

        // Check if item already in cart
        if (isset($cart[$cartKey])) {
            $newQuantity = $cart[$cartKey]['quantity'] + $validated['quantity'];
            
            if ($newQuantity > $productSize->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Maximum available: ' . $productSize->stock_quantity,
                ], 400);
            }

            $cart[$cartKey]['quantity'] = $newQuantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $validated['product_id'],
                'size' => $validated['size'],
                'quantity' => $validated['quantity'],
                'added_at' => now()->toDateTimeString(),
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully!',
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $cartKey)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = session('cart', []);

        if (!isset($cart[$cartKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.',
            ], 404);
        }

        $item = $cart[$cartKey];
        $product = Product::find($item['product_id']);

        // Check stock availability
        $productSize = $product->productSizes()
            ->where('size', $item['size'])
            ->first();

        if (!$productSize || $productSize->stock_quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . ($productSize->stock_quantity ?? 0),
            ], 400);
        }

        $cart[$cartKey]['quantity'] = $validated['quantity'];
        session(['cart' => $cart]);

        // Calculate new totals
        $activePrice = $product->active_price;
        $itemTotal = $activePrice * $validated['quantity'];
        $subtotal = 0;
        foreach ($cart as $cartItem) {
            $p = Product::find($cartItem['product_id']);
            $subtotal += $p->active_price * $cartItem['quantity'];
        }

        $tax = $subtotal * 0.1;
        $shippingCost = $subtotal > 100 ? 0 : 10;
        $total = $subtotal + $tax + $shippingCost;

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'item_total' => $itemTotal,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shippingCost,
            'total' => $total,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($cartKey)
    {
        $cart = session('cart', []);

        if (!isset($cart[$cartKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.',
            ], 404);
        }

        unset($cart[$cartKey]);
        session(['cart' => $cart]);

        // Calculate new totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            $subtotal += $product->active_price * $item['quantity'];
        }

        $tax = $subtotal * 0.1;
        $shippingCost = $subtotal > 100 ? 0 : 10;
        $total = $subtotal + $tax + $shippingCost;

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => count($cart),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shippingCost,
            'total' => $total,
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!',
        ]);
    }

    /**
     * Get cart count
     */
    public function count()
    {
        $cart = session('cart', []);
        
        return response()->json([
            'count' => count($cart),
        ]);
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        // In production, validate coupon against database
        // For now, hardcoded example coupons
        $coupons = [
            'WELCOME10' => ['type' => 'percentage', 'value' => 10, 'min_purchase' => 50],
            'SAVE20' => ['type' => 'percentage', 'value' => 20, 'min_purchase' => 100],
            'FLAT15' => ['type' => 'fixed', 'value' => 15, 'min_purchase' => 75],
        ];

        $couponCode = strtoupper($validated['coupon_code']);

        if (!isset($coupons[$couponCode])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 400);
        }

        $coupon = $coupons[$couponCode];
        $cart = session('cart', []);
        
        // Calculate subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            $subtotal += $product->active_price * $item['quantity'];
        }

        // Check minimum purchase requirement
        if ($subtotal < $coupon['min_purchase']) {
            return response()->json([
                'success' => false,
                'message' => "Minimum purchase of $" . $coupon['min_purchase'] . " required for this coupon.",
            ], 400);
        }

        // Calculate discount
        $discount = 0;
        if ($coupon['type'] === 'percentage') {
            $discount = ($subtotal * $coupon['value']) / 100;
        } else {
            $discount = $coupon['value'];
        }

        session(['coupon' => [
            'code' => $couponCode,
            'discount' => $discount,
        ]]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $discount,
        ]);
    }

    /**
     * Remove coupon
     */
    public function removeCoupon()
    {
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
        ]);
    }

    /**
     * Save cart for later (requires auth)
     */
    public function saveForLater()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to save cart.',
            ], 401);
        }

        $cart = session('cart', []);
        
        // In production, save to database
        // For now, keep in session with different key
        session(['saved_cart' => $cart]);
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart saved for later!',
        ]);
    }

    /**
     * Restore saved cart
     */
    public function restoreSaved()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to restore cart.',
            ], 401);
        }

        $savedCart = session('saved_cart', []);
        
        if (empty($savedCart)) {
            return response()->json([
                'success' => false,
                'message' => 'No saved cart found.',
            ], 404);
        }

        session(['cart' => $savedCart]);
        session()->forget('saved_cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart restored successfully!',
            'cart_count' => count($savedCart),
        ]);
    }
}
