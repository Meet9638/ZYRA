<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceipt;

class OrderController extends Controller
{
    public function __construct()
    {
        // No dependencies
    }

    /**
     * Display user's orders
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        $order->load(['orderItems.product.productSizes']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user      = Auth::user();
        $cartItems = session('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty');
        }

        $subtotal = 0;
        $items    = [];

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $activePrice = $product->active_price;
                $itemTotal = $activePrice * $item['quantity'];
                $subtotal += $itemTotal;

                $items[] = [
                    'product'           => $product,
                    'quantity'          => $item['quantity'],
                    'size'              => $item['size'],
                    'unit_price'        => $activePrice,
                    'total'             => $itemTotal,
                ];
            }
        }

        $tax          = $subtotal * 0.1;
        $shippingCost = $subtotal > 100 ? 0 : 10;
        $total        = $subtotal + $tax + $shippingCost;

        return view('users.orders.checkout', compact('items', 'subtotal', 'tax', 'shippingCost', 'total'));
    }

    /**
     * Process order placement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address'           => 'required|string|max:500',
            'billing_address'            => 'required|string|max:500',
            'phone'                      => 'required|string|max:20',
            'notes'                      => 'nullable|string|max:1000',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.size'               => 'required|string',
            'items.*.quantity'           => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $user       = Auth::user();
            $subtotal   = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product     = Product::findOrFail($item['product_id']);
                $productSize = $product->productSizes()->where('size', $item['size'])->first();

                if (!$productSize || $productSize->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name} in size {$item['size']}");
                }

                $activePrice = $product->active_price;
                $itemTotal  = $activePrice * $item['quantity'];
                $subtotal  += $itemTotal;

                $orderItems[] = [
                    'product_id'                => $product->id,
                    'size'                      => $item['size'],
                    'quantity'                  => $item['quantity'],
                    'unit_price'                => $activePrice,
                    'total_price'               => $itemTotal,
                ];
            }

            $tax         = $subtotal * 0.1;
            $shippingCost = $subtotal > 100 ? 0 : 10;
            $totalAmount = $subtotal + $tax + $shippingCost;

            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => $this->generateOrderNumber(),
                'subtotal'         => $subtotal,
                'tax'              => $tax,
                'shipping_cost'    => $shippingCost,
                'total_amount'     => $totalAmount,
                'status'           => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'billing_address'  => $validated['billing_address'],
                'phone'            => $validated['phone'],
                'notes'            => $validated['notes'] ?? null,
            ]);

            foreach ($orderItems as $itemData) {
                $orderItem   = $order->orderItems()->create($itemData);
                $product     = Product::find($itemData['product_id']);
                $productSize = $product->productSizes()->where('size', $itemData['size'])->first();

                $productSize->decrement('stock_quantity', $itemData['quantity']);
                $product->decrement('stock_quantity', $itemData['quantity']);

            }

            session()->forget('cart');

            DB::commit();

            try {
                Mail::to($user->email)->send(new OrderReceipt($order));
            } catch (\Exception $mailException) {
                Log::error('Failed to send order receipt: ' . $mailException->getMessage());
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to place order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()
                ->with('error', 'Cannot cancel order with status: ' . $order->status);
        }

        DB::beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                $product     = $item->product;
                $productSize = $product->productSizes()->where('size', $item->size)->first();

                if ($productSize) {
                    $productSize->increment('stock_quantity', $item->quantity);
                }
                $product->increment('stock_quantity', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
