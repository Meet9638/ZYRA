<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page.
     */
    public function index()
    {
        $items = [];

        if (Auth::check()) {
            // Authenticated: Eager-load wishlisted products with category and sizes to prevent N+1 queries
            $wishlistRecords = Wishlist::where('user_id', Auth::id())
                ->with(['product.category', 'product.productSizes'])
                ->latest()
                ->get();

            foreach ($wishlistRecords as $record) {
                if ($record->product && $record->product->is_active) {
                    $items[] = $record->product;
                }
            }
        } else {
            // Guest: Fetch product IDs from session and load details
            $sessionWishlist = session('wishlist', []);
            if (!empty($sessionWishlist)) {
                $items = Product::whereIn('id', $sessionWishlist)
                    ->where('is_active', true)
                    ->with(['category', 'productSizes'])
                    ->get();
            }
        }

        return view('wishlist.index', compact('items'));
    }

    /**
     * Toggle a product's wishlist status via AJAX.
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $productId = $validated['product_id'];
        $product = Product::findOrFail($productId);

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This product is no longer available.',
            ], 400);
        }

        if (Auth::check()) {
            $userId = Auth::id();
            $wishlistItem = Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($wishlistItem) {
                $wishlistItem->delete();
                $action = 'removed';
                $message = 'Item removed from your wishlist.';
            } else {
                Wishlist::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ]);
                $action = 'added';
                $message = 'Item added to your wishlist!';
            }
            // Clear cache to reflect the new state
            Wishlist::clearCache();
        } else {
            // Guest handling via session
            $sessionWishlist = session('wishlist', []);

            if (in_array($productId, $sessionWishlist)) {
                $sessionWishlist = array_values(array_diff($sessionWishlist, [$productId]));
                $action = 'removed';
                $message = 'Item removed from your wishlist.';
            } else {
                $sessionWishlist[] = $productId;
                $action = 'added';
                $message = 'Item added to your wishlist!';
            }

            session(['wishlist' => $sessionWishlist]);
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'message' => $message,
            'wishlist_count' => Wishlist::getWishlistCount(),
        ]);
    }

    /**
     * Get live wishlist item count via AJAX.
     */
    public function count()
    {
        return response()->json([
            'count' => Wishlist::getWishlistCount(),
        ]);
    }

    /**
     * Move a wishlisted item to the active shopping cart bag (requires auth).
     */
    public function moveToCart(Request $request, $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to perform this action.',
                'redirect' => route('login'),
            ], 401);
        }

        $validated = $request->validate([
            'size' => 'required|string',
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        $quantity = $validated['quantity'] ?? 1;
        $product = Product::findOrFail($productId);

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This product is no longer available.',
            ], 400);
        }

        // Validate stock of chosen size
        $productSize = $product->productSizes()
            ->where('size', $validated['size'])
            ->first();

        if (!$productSize || $productSize->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Selected size is currently out of stock.',
            ], 400);
        }

        // Add to shopping cart session (conforms with CartController standard)
        $cart = session('cart', []);
        $cartKey = $productId . '_' . $validated['size'];

        if (isset($cart[$cartKey])) {
            $newQuantity = $cart[$cartKey]['quantity'] + $quantity;
            
            if ($newQuantity > $productSize->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Maximum available: ' . $productSize->stock_quantity,
                ], 400);
            }
            $cart[$cartKey]['quantity'] = $newQuantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $productId,
                'size' => $validated['size'],
                'quantity' => $quantity,
                'added_at' => now()->toDateTimeString(),
            ];
        }

        session(['cart' => $cart]);

        // Remove from wishlist database table
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        // Clear wishlist cache
        Wishlist::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Item successfully moved to your Atelier Bag!',
            'wishlist_count' => Wishlist::getWishlistCount(),
            'cart_count' => count($cart),
        ]);
    }
}
