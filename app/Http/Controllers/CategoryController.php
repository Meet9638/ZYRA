<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display all active categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show category with its products
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $category->products()
            ->where('is_active', true)
            ->with('productSizes')
            ->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }

    /**
     * Get all categories (API)
     */
    public function apiIndex()
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'image']);

        return response()->json([
            'success'    => true,
            'categories' => $categories,
        ]);
    }

    /**
     * Get category products (API)
     */
    public function apiProducts($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $category->products()
            ->where('is_active', true)
            ->with('productSizes')
            ->get();

        return response()->json([
            'success'  => true,
            'category' => [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'description' => $category->description,
            ],
            'products' => $products,
        ]);
    }
}
