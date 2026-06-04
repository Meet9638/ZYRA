<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Cache user's wishlisted product IDs to avoid N+1 query overhead.
     */
    protected static $cachedUserWishlistIds = null;

    /**
     * Relationship: Wishlist belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Wishlist belongs to a Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Determine if a specific product is wishlisted by the current user (or guest in session).
     * Highly optimized using request-caching to eliminate N+1 queries on index grids.
     *
     * @param int $productId
     * @return bool
     */
    public static function isProductWishlisted($productId)
    {
        if (Auth::check()) {
            if (self::$cachedUserWishlistIds === null) {
                self::$cachedUserWishlistIds = self::where('user_id', Auth::id())
                    ->pluck('product_id')
                    ->toArray();
            }
            return in_array($productId, self::$cachedUserWishlistIds);
        }

        // Guest handling via session
        $sessionWishlist = session('wishlist', []);
        return in_array($productId, $sessionWishlist);
    }

    /**
     * Get the active wishlist count for either the logged-in user or guest session.
     *
     * @return int
     */
    public static function getWishlistCount()
    {
        if (Auth::check()) {
            return self::where('user_id', Auth::id())->count();
        }

        return count(session('wishlist', []));
    }

    /**
     * Merge wishlist items stored in guest session into the database upon successful login/register.
     *
     * @param User $user
     * @return void
     */
    public static function mergeSessionWishlist($user)
    {
        $sessionWishlist = session('wishlist', []);
        
        if (!empty($sessionWishlist) && is_array($sessionWishlist)) {
            foreach ($sessionWishlist as $productId) {
                // Ensure the product exists before wishlisting
                if (Product::where('id', $productId)->exists()) {
                    self::firstOrCreate([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                    ]);
                }
            }
            // Clear the guest wishlist from session after merging
            session()->forget('wishlist');
            
            // Clear static cache to reflect the new merged state
            self::clearCache();
        }
    }

    /**
     * Clear the static request-cache.
     */
    public static function clearCache()
    {
        self::$cachedUserWishlistIds = null;
    }
}
