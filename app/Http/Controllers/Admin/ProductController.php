<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all products (including inactive)
     */
    public function index(Request $request)
    {
        $this->authorize('admin');

        $query = Product::with(['category', 'productSizes']);

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Only show active products by default unless explicitly asked for inactive or all
        $status = $request->get('status', 'active');
        if ($status !== 'all') {
            $query->where('is_active', $status === 'active');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('sku', 'like', "%{$search}%")
                                      ->orWhere('description', 'like', "%{$search}%"));
        }

        switch ($request->get('sort', 'created_at')) {
            case 'name':       $query->orderBy('name', 'asc'); break;
            case 'price-low':  $query->orderBy('price', 'asc'); break;
            case 'price-high': $query->orderBy('price', 'desc'); break;
            case 'stock-low':  $query->orderBy('stock_quantity', 'asc'); break;
            default:           $query->orderBy('created_at', 'desc');
        }

        $products   = $query->paginate(15);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'category_id'             => 'required|exists:categories,id',
            'description'             => 'required|string',
            'price'                   => 'required|numeric|min:0',
            'discount_price'          => 'nullable|numeric|min:0|lt:price',
            'on_sale'                 => 'boolean',
            'sku'                     => 'required|string|unique:products,sku',
            'stock_quantity'          => 'required|integer|min:0',
            'gender'                  => 'required|in:men,women,unisex',
            'main_image'              => 'required|image|max:2048',
            'additional_images'       => 'nullable|array',
            'additional_images.*'     => 'nullable|image|max:2048',
            'sizes'                   => 'required|array|min:1',
        ]);

        $mainImagePath     = $request->file('main_image')->store('products', 'public');
        $additionalImages  = [];

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $additionalImages[] = $image->store('products', 'public');
            }
        }

        $product = Product::create([
            'name'               => $validated['name'],
            'slug'               => Str::slug($validated['name']),
            'category_id'        => $validated['category_id'],
            'description'        => $validated['description'],
            'price'              => $validated['price'],
            'discount_price'     => $validated['discount_price'],
            'on_sale'            => $validated['on_sale'] ?? false,
            'sku'                => $validated['sku'],
            'stock_quantity'     => $validated['stock_quantity'],
            'gender'             => $validated['gender'],
            'main_image'         => $mainImagePath,
            'additional_images'  => empty($additionalImages) ? null : json_encode($additionalImages),
            'is_featured'        => $request->boolean('is_featured'),
            'is_active'          => $request->boolean('is_active', true),
        ]);

        foreach ($validated['sizes'] as $sizeString) {
            $product->productSizes()->create([
                'size'           => $sizeString,
                'stock_quantity' => 0, // Default to 0 as UI doesn't capture per-size stock yet
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show edit product form
     */
    public function edit(Product $product)
    {
        $this->authorize('admin');

        $categories = Category::where('is_active', true)->get();
        $product->load('productSizes');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update a product
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'description'        => 'required|string',
            'price'              => 'required|numeric|min:0',
            'discount_price'     => 'nullable|numeric|min:0|lt:price',
            'on_sale'            => 'boolean',
            'sku'                => 'required|string|unique:products,sku,' . $product->id,
            'stock_quantity'     => 'required|integer|min:0',
            'gender'               => 'required|in:men,women,unisex',
            'main_image'           => 'nullable|image|max:2048',
            'additional_images'    => 'nullable|array',
            'additional_images.*'  => 'nullable|image|max:2048',
            'sizes'                => 'required|array|min:1',
        ]);

        if ($request->hasFile('main_image')) {
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        // Handle additional images
        $existingAdditionalImages = $product->additional_images ?? [];
        
        // Remove specified additional images
        if ($request->has('remove_additional_images')) {
            $imagesToRemove = $request->input('remove_additional_images');
            if (is_array($imagesToRemove)) {
                foreach ($imagesToRemove as $imageToRemove) {
                    // Delete from storage
                    Storage::disk('public')->delete($imageToRemove);
                    // Remove from array
                    $existingAdditionalImages = array_values(array_filter($existingAdditionalImages, function($img) use ($imageToRemove) {
                        return $img !== $imageToRemove;
                    }));
                }
            }
        }
        
        if ($request->hasFile('additional_images')) {
            $newAdditionalImages = [];
            foreach ($request->file('additional_images') as $image) {
                $newAdditionalImages[] = $image->store('products', 'public');
            }
            // Merge with existing images
            $allAdditionalImages = array_merge($existingAdditionalImages, $newAdditionalImages);
            $validated['additional_images'] = empty($allAdditionalImages) ? null : json_encode($allAdditionalImages);
        } else {
            // Keep existing additional images if no new ones uploaded
            $validated['additional_images'] = empty($existingAdditionalImages) ? null : json_encode($existingAdditionalImages);
        }

        $product->update([
            'name'           => $validated['name'],
            'slug'           => Str::slug($validated['name']),
            'category_id'    => $validated['category_id'],
            'description'    => $validated['description'],
            'price'          => $validated['price'],
            'discount_price' => $validated['discount_price'],
            'on_sale'        => $validated['on_sale'] ?? $product->on_sale,
            'sku'            => $validated['sku'],
            'stock_quantity' => $validated['stock_quantity'],
            'gender'         => $validated['gender'],
            'main_image'     => $validated['main_image'] ?? $product->main_image,
            'additional_images' => $validated['additional_images'],
            'is_active'      => $request->boolean('is_active', $product->is_active),
            'is_featured'    => $request->boolean('is_featured'),
        ]);

        // Sync sizes
        $product->productSizes()->delete();
        foreach ($validated['sizes'] as $sizeString) {
            $product->productSizes()->create([
                'size'           => $sizeString,
                'stock_quantity' => 0, // Fallback as per current UI
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product
     */
    public function destroy(Product $product)
    {
        $this->authorize('admin');

        try {
            // Remove main image if it exists
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            // Remove additional images if they exist
            if ($product->additional_images) {
                $images = json_decode($product->additional_images, true);
                if (is_array($images)) {
                    foreach ($images as $img) {
                        Storage::disk('public')->delete($img);
                    }
                }
            }

            // Attempt to hard delete from database
            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product permanently deleted.');

        } catch (\Illuminate\Database\QueryException $e) {
            // If the product is tied to an order (Foreign Key Constraint),
            // gracefully fallback to setting it inactive so we preserve order history.
            $product->update(['is_active' => false]);
            return redirect()->route('admin.products.index')
                ->with('warning', 'Product cannot be fully deleted because it is part of a previous order. It has been set to INACTIVE instead.');
        }
    }

    /**
     * Toggle product featured status
     */
    public function toggleFeatured(Product $product)
    {
        $this->authorize('admin');

        $product->update(['is_featured' => !$product->is_featured]);

        return response()->json([
            'success'     => true,
            'is_featured' => $product->is_featured,
        ]);
    }

    /**
     * Bulk update product stock
     */
    public function bulkUpdateStock(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'updates'                    => 'required|array',
            'updates.*.product_id'       => 'required|exists:products,id',
            'updates.*.stock_quantity'   => 'required|integer|min:0',
        ]);

        foreach ($validated['updates'] as $update) {
            Product::find($update['product_id'])->update([
                'stock_quantity' => $update['stock_quantity'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Stock updated successfully']);
    }
}
