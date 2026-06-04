<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all categories including inactive ones
     */
    public function index(Request $request)
    {
        $this->authorize('admin');

        $query = Category::withCount('products');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show create category form
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|max:5024',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'],
            'image'       => $imagePath,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show edit category form
     */
    public function edit(Category $category)
    {
        $this->authorize('admin');

        $category->loadCount('products');

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|max:2048',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'image'       => $validated['image'] ?? $category->image,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        $this->authorize('admin');

        if ($category->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with existing products. Please reassign or delete products first.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Get category statistics
     */
    public function statistics(Category $category)
    {
        $this->authorize('admin');

        $stats = [
            'total_products'    => $category->products()->count(),
            'active_products'   => $category->products()->where('is_active', true)->count(),
            'total_stock'       => $category->products()->sum('stock_quantity'),
            'total_value'       => $category->products()->sum('price'),
            'featured_products' => $category->products()->where('is_featured', true)->count(),
            'out_of_stock'      => $category->products()->where('stock_quantity', 0)->count(),
        ];

        return response()->json([
            'success'    => true,
            'category'   => $category->name,
            'statistics' => $stats,
        ]);
    }
}
