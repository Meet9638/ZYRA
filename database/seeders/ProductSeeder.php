<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample product images directory
        $this->createSampleImages();

        // Get or create categories
        $categories = $this->getOrCreateCategories();

        // Sample products with images
        $products = [
            [
                'name' => 'AI-Optimized Smart Shirt',
                'slug' => 'ai-optimized-smart-shirt',
                'description' => 'Revolutionary smart shirt with AI-powered fit optimization. Features moisture-wicking fabric, adaptive sizing, and real-time comfort monitoring.',
                'price' => 89.99,
                'discount_price' => 79.99,
                'sku' => 'AI-SHIRT-001',
                'stock_quantity' => 150,
                'main_image' => 'products/smart-shirt-1.jpg',
                'additional_images' => json_encode([
                    'products/smart-shirt-2.jpg',
                    'products/smart-shirt-3.jpg',
                    'products/smart-shirt-4.jpg'
                ]),
                'gender' => 'men',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories['shirts'],
            ],
            [
                'name' => 'Neural Network Dress',
                'slug' => 'neural-network-dress',
                'description' => 'Elegant dress designed with neural network algorithms for perfect fit. Features adaptive fabric technology and AI-enhanced comfort zones.',
                'price' => 129.99,
                'discount_price' => 109.99,
                'sku' => 'AI-DRESS-001',
                'stock_quantity' => 75,
                'main_image' => 'products/neural-dress-1.jpg',
                'additional_images' => json_encode([
                    'products/neural-dress-2.jpg',
                    'products/neural-dress-3.jpg'
                ]),
                'gender' => 'women',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories['dresses'],
            ],
            [
                'name' => 'Machine Learning Jeans',
                'slug' => 'machine-learning-jeans',
                'description' => 'Premium denim jeans with machine learning-enhanced fit technology. Self-adjusting waistband and AI-optimized pocket placement.',
                'price' => 159.99,
                'sku' => 'AI-JEANS-001',
                'stock_quantity' => 200,
                'main_image' => 'products/ml-jeans-1.jpg',
                'additional_images' => json_encode([
                    'products/ml-jeans-2.jpg',
                    'products/ml-jeans-3.jpg',
                    'products/ml-jeans-4.jpg'
                ]),
                'gender' => 'unisex',
                'is_featured' => false,
                'is_active' => true,
                'category_id' => $categories['jeans'],
            ],
            [
                'name' => 'Deep Learning Hoodie',
                'slug' => 'deep-learning-hoodie',
                'description' => 'Comfortable hoodie with deep learning thermal regulation. Features smart temperature control and AI-powered fabric adaptation.',
                'price' => 119.99,
                'discount_price' => 99.99,
                'sku' => 'AI-HOODIE-001',
                'stock_quantity' => 100,
                'main_image' => 'products/dl-hoodie-1.jpg',
                'additional_images' => json_encode([
                    'products/dl-hoodie-2.jpg',
                    'products/dl-hoodie-3.jpg'
                ]),
                'gender' => 'men',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories['hoodies'],
            ],
            [
                'name' => 'Computer Vision Blazer',
                'slug' => 'computer-vision-blazer',
                'description' => 'Professional blazer with computer vision-enhanced styling. Features wrinkle-resistant fabric and AI-optimized shoulder padding.',
                'price' => 249.99,
                'sku' => 'AI-BLAZER-001',
                'stock_quantity' => 50,
                'main_image' => 'products/cv-blazer-1.jpg',
                'additional_images' => json_encode([
                    'products/cv-blazer-2.jpg',
                    'products/cv-blazer-3.jpg'
                ]),
                'gender' => 'men',
                'is_featured' => false,
                'is_active' => true,
                'category_id' => $categories['blazers'],
            ],
            [
                'name' => 'AI Sports Performance Tee',
                'slug' => 'ai-sports-performance-tee',
                'description' => 'High-performance sports t-shirt with AI moisture management. Features real-time sweat analysis and adaptive ventilation.',
                'price' => 69.99,
                'discount_price' => 59.99,
                'sku' => 'AI-SPORTS-001',
                'stock_quantity' => 300,
                'main_image' => 'products/sports-tee-1.jpg',
                'additional_images' => json_encode([
                    'products/sports-tee-2.jpg',
                    'products/sports-tee-3.jpg'
                ]),
                'gender' => 'unisex',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories['sports'],
            ],
            [
                'name' => 'Neural Interface Sneakers',
                'slug' => 'neural-interface-sneakers',
                'description' => 'Next-generation sneakers with neural interface technology. Features self-lacing system and AI gait analysis.',
                'price' => 189.99,
                'sku' => 'AI-SNEAKERS-001',
                'stock_quantity' => 120,
                'main_image' => 'products/neural-sneakers-1.jpg',
                'additional_images' => json_encode([
                    'products/neural-sneakers-2.jpg',
                    'products/neural-sneakers-3.jpg',
                    'products/neural-sneakers-4.jpg'
                ]),
                'gender' => 'unisex',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories['footwear'],
            ],
            [
                'name' => 'Smart Casual Pants',
                'slug' => 'smart-casual-pants',
                'description' => 'Intelligent casual pants with AI-enhanced comfort. Features temperature regulation and smart fabric technology.',
                'price' => 139.99,
                'sku' => 'AI-PANTS-001',
                'stock_quantity' => 180,
                'main_image' => 'products/smart-pants-1.jpg',
                'additional_images' => json_encode([
                    'products/smart-pants-2.jpg',
                    'products/smart-pants-3.jpg'
                ]),
                'gender' => 'men',
                'is_featured' => false,
                'is_active' => true,
                'category_id' => $categories['pants'],
            ],
            [
                'name' => 'AI Summer Collection Dress',
                'slug' => 'ai-summer-collection-dress',
                'description' => 'Lightweight summer dress with AI climate adaptation. Features UV protection and smart cooling technology.',
                'price' => 149.99,
                'discount_price' => 119.99,
                'sku' => 'AI-SUMMER-001',
                'stock_quantity' => 80,
                'main_image' => 'products/summer-dress-1.jpg',
                'additional_images' => json_encode([
                    'products/summer-dress-2.jpg',
                    'products/summer-dress-3.jpg'
                ]),
                'gender' => 'women',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => $categories['dresses'],
            ],
        ];

        // Insert products and sizes
        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Seed sizes (XS, S, M, L, XL, XXL)
            $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
            foreach ($sizes as $size) {
                \App\Models\ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'stock_quantity' => rand(10, 50),
                ]);
            }
        }

        $this->command->info('Sample products with images seeded successfully!');
    }

    private function createSampleImages(): void
    {
        // Create products directory structure
        $directories = [
            'products',
            'products/thumbnails',
        ];

        foreach ($directories as $dir) {
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
        }

        // Create placeholder image descriptions
        $this->command->info('Created product image directories');
    }

    private function getOrCreateCategories(): array
    {
        return [
            'shirts' => Category::firstOrCreate(['name' => 'Shirts', 'slug' => 'shirts'])->id,
            'dresses' => Category::firstOrCreate(['name' => 'Dresses', 'slug' => 'dresses'])->id,
            'jeans' => Category::firstOrCreate(['name' => 'Jeans', 'slug' => 'jeans'])->id,
            'hoodies' => Category::firstOrCreate(['name' => 'Hoodies', 'slug' => 'hoodies'])->id,
            'blazers' => Category::firstOrCreate(['name' => 'Blazers', 'slug' => 'blazers'])->id,
            'sports' => Category::firstOrCreate(['name' => 'Sports', 'slug' => 'sports'])->id,
            'footwear' => Category::firstOrCreate(['name' => 'Footwear', 'slug' => 'footwear'])->id,
            'pants' => Category::firstOrCreate(['name' => 'Pants', 'slug' => 'pants'])->id,
        ];
    }
}
