<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of active products
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'productSizes'])->where('is_active', true);

        if ($request->has('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('description', 'like', "%{$search}%"));
        }

        switch ($request->get('sort', 'featured')) {
            case 'price-low':  $query->orderBy('price', 'asc'); break;
            case 'price-high': $query->orderBy('price', 'desc'); break;
            case 'newest':     $query->orderBy('created_at', 'desc'); break;
            default:           $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);

        return view('products.index', compact('products'));
    }

    /**
     * Display a single product
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'productSizes'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
