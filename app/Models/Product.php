<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\OrderItem;

class Product extends Model
{
    protected $fillable = [
        'category_id','name','slug','description','price',
        'discount_price','on_sale','sku','stock_quantity','main_image',
        'additional_images','gender','is_featured','is_active'
    ];

    protected $casts = [
        'additional_images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function productSizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sizeRecommendations()
    {
        return $this->hasMany(SizeRecommendation::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    // Accessor for total_sold (calculated from order items)
    public function getTotalSoldAttribute()
    {
        return $this->orderItems()->sum('quantity');
    }

    // Accessor for total_revenue (calculated from order items)
    public function getTotalRevenueAttribute()
    {
        // Assuming each order item has a 'price' field representing unit price
        return $this->orderItems()->sum(
            DB::raw('quantity * unit_price')
        );
    }

    // Accessor for active selling price
    public function getActivePriceAttribute()
    {
        if ($this->on_sale && $this->discount_price && $this->discount_price < $this->price) {
            return $this->discount_price;
        }
        return $this->price;
    }

    // Accessor to determine if product is effectively on sale
    public function getIsEffectivelyOnSaleAttribute()
    {
        return $this->on_sale && $this->discount_price && $this->discount_price < $this->price;
    }

    // Accessor for discount percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->is_effectively_on_sale) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    // Accessor for main image with fallback
    public function getMainImageUrlAttribute()
    {
        if ($this->main_image) {
            return asset('storage/' . $this->main_image);
        }
        return asset('images/placeholder-product.svg');
    }

    // Accessor for additional images with fallback
    public function getAdditionalImagesUrlsAttribute()
    {
        $urls = [];
        if ($this->additional_images && is_array($this->additional_images)) {
            foreach ($this->additional_images as $image) {
                if ($image) {
                    $urls[] = asset('storage/' . $image);
                }
            }
        }
        return $urls;
    }

    // Accessor for all images (main + additional)
    public function getAllImagesAttribute()
    {
        $allImages = [$this->main_image_url];
        foreach ($this->additional_images_urls as $url) {
            $allImages[] = $url;
        }
        return array_filter($allImages);
    }


}