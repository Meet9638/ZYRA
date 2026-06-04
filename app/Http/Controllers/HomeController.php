<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the landing page with featured products.
     */
    public function index()
    {
        // Fetch featured products or just the latest 6 products
        $products = Product::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        // If no featured products, just take the latest active ones
        if ($products->isEmpty()) {
            $products = Product::where('is_active', true)
                ->latest()
                ->take(6)
                ->get();
        }

        return view('welcome', compact('products'));
    }
}
