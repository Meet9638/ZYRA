<?php
use App\Models\Product;

Product::all()->each(function($p) {
    if ($p->productSizes()->count() === 0) {
        foreach (['S', 'M', 'L', 'XL'] as $size) {
            $p->productSizes()->create([
                'size' => $size,
                'stock_quantity' => 10
            ]);
        }
    }
});
echo "Sizes populated successfully.";
