<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * View all orders
     */
    public function index(Request $request)
    {
        $this->authorize('admin');

        $query = Order::with(['user', 'orderItems.product']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * View order details
     */
    public function show(Order $order)
    {
        $this->authorize('admin');

        $order->load(['user', 'orderItems.product.productSizes']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'shipped') {
            $order->update(['shipped_at' => now()]);
        } elseif ($validated['status'] === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'status'  => $order->status,
        ]);
    }

    /**
     * Get order statistics for a given period
     */
    public function statistics(Request $request)
    {
        $this->authorize('admin');

        $period    = $request->get('period', 'week');
        $startDate = match ($period) {
            'day'   => now()->startOfDay(),
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year'  => now()->startOfYear(),
            default => now()->startOfWeek(),
        };

        $stats = [
            'total_orders'       => Order::where('created_at', '>=', $startDate)->count(),
            'total_revenue'      => Order::where('created_at', '>=', $startDate)
                                        ->whereNotIn('status', ['cancelled', 'refunded'])
                                        ->sum('total_amount'),
            'pending_orders'     => Order::where('status', 'pending')
                                        ->where('created_at', '>=', $startDate)->count(),
            'completed_orders'   => Order::where('status', 'delivered')
                                        ->where('created_at', '>=', $startDate)->count(),
            'cancelled_orders'   => Order::where('status', 'cancelled')
                                        ->where('created_at', '>=', $startDate)->count(),
            'average_order_value'=> Order::where('created_at', '>=', $startDate)
                                        ->whereNotIn('status', ['cancelled', 'refunded'])
                                        ->avg('total_amount'),
        ];

        return response()->json([
            'success'    => true,
            'period'     => $period,
            'start_date' => $startDate->toDateString(),
            'statistics' => $stats,
        ]);
    }

    /**
     * Export all orders to CSV
     */
    public function export()
    {
        $this->authorize('admin');

        $orders   = Order::with(['user', 'orderItems.product'])->orderBy('created_at', 'desc')->get();
        $filename = 'orders_' . now()->format('Y-m-d_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Order Number', 'Customer Name', 'Customer Email',
                'Date', 'Status', 'Items', 'Subtotal', 'Tax', 'Shipping', 'Total',
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name,
                    $order->user->email,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->status,
                    $order->orderItems->count(),
                    $order->subtotal,
                    $order->tax,
                    $order->shipping_cost,
                    $order->total_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete an order
     */
    public function destroy(Order $order)
    {
        $this->authorize('admin');

        // Restore stock for each item before deleting the order
        foreach ($order->orderItems as $item) {
            $product     = $item->product;
            $productSize = $product->productSizes()->where('size', $item->size)->first();

            if ($productSize) {
                $productSize->increment('stock_quantity', $item->quantity);
            }
            $product->increment('stock_quantity', $item->quantity);
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
                         ->with('success', 'Order has been successfully deleted.');
    }
}
