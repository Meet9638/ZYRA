<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('pages.track-order');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->whereHas('user', function($query) use ($request) {
                $query->where('email', $request->email);
            })
            ->with(['orderItems.product'])
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found or email does not match. Please try again.')->withInput();
        }

        return view('pages.track-order', compact('order'));
    }
}
