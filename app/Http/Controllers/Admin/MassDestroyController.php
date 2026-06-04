<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;

class MassDestroyController extends Controller
{
    public function destroy(Request $request, $model)
    {
        $this->authorize('admin');
        
        $ids = $request->input('ids');
        
        if (empty($ids)) {
            return back()->with('error', 'No items selected for deletion.');
        }

        switch ($model) {
            case 'product':
                Product::whereIn('id', $ids)->delete();
                break;
            case 'category':
                Category::whereIn('id', $ids)->delete();
                break;
            case 'user':
                User::whereIn('id', $ids)->delete();
                break;
            case 'order':
                Order::whereIn('id', $ids)->delete();
                break;
            default:
                return back()->with('error', 'Invalid model for mass deletion.');
        }

        return back()->with('success', count($ids) . ' item(s) permanently deleted.');
    }
}
