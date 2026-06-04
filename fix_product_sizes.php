<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::all();
foreach($products as $product) {
    if($product->productSizes()->count() == 0) {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        foreach($sizes as $size) {
            App\Models\ProductSize::create([
                'product_id' => $product->id,
                'size' => $size,
                'stock_quantity' => rand(10, 50)
            ]);
        }
        echo "Sizes added to product: " . $product->name . "\n";
    } else {
        echo "Sizes already exist for product: " . $product->name . "\n";
    }
}
echo "Done!\n";
