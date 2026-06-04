<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReturnController extends Controller
{
    use AuthorizesRequests;
    public function __construct()
    {
        // No dependencies
    }

    /**
     * Display user's returns
     */
    public function index()
    {
        $user = Auth::user();
        
        $returns = ReturnModel::whereHas('orderItem.order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['orderItem.product', 'orderItem.order'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('returns.index', compact('returns'));
    }

    /**
     * Show return request form
     */
    public function create(OrderItem $orderItem)
    {
        // Ensure user owns this order
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if item is already returned or has pending return
        if ($orderItem->return) {
            return redirect()->route('orders.show', $orderItem->order)
                ->with('error', 'This item already has a return request');
        }

        // Check if order is delivered (can only return delivered items)
        if ($orderItem->order->status !== 'delivered') {
            return redirect()->route('orders.show', $orderItem->order)
                ->with('error', 'Can only return delivered items');
        }

        // Check return window (e.g., 30 days)
        $returnDeadline = $orderItem->order->delivered_at?->addDays(30);
        if (!$returnDeadline || now()->isAfter($returnDeadline)) {
            return redirect()->route('orders.show', $orderItem->order)
                ->with('error', 'Return window has expired (30 days from delivery)');
        }

        return view('returns.create', compact('orderItem'));
    }

    /**
     * Store return request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'reason' => 'required|in:too_small,too_large,wrong_item,damaged,quality_issue,not_as_described,other',
            'detailed_reason' => 'nullable|string|max:1000',
            'preferred_replacement_size' => 'nullable|string|max:10',
        ]);

        $orderItem = OrderItem::findOrFail($validated['order_item_id']);

        // Verify ownership
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if already has return
        if ($orderItem->return) {
            return redirect()->route('orders.show', $orderItem->order)
                ->with('error', 'This item already has a return request');
        }

        DB::beginTransaction();
        try {
            // Create return request
            $return = ReturnModel::create([
                'order_item_id' => $orderItem->id,
                'return_number' => $this->generateReturnNumber(),
                'reason' => $validated['reason'],
                'detailed_reason' => $validated['detailed_reason'],
                'returned_size' => $orderItem->size,
                'preferred_replacement_size' => $validated['preferred_replacement_size'],
                'status' => 'requested',
            ]);


            DB::commit();

            return redirect()->route('returns.show', $return)
                ->with('success', 'Return request submitted successfully! Return number: ' . $return->return_number);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to submit return request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display return details
     */
    public function show(ReturnModel $return)
    {
        // Ensure user can only view their own returns
        if ($return->orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $return->load(['orderItem.product', 'orderItem.order']);

        return view('returns.show', compact('return'));
    }

    /**
     * Admin: View all returns
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('admin');

        $query = ReturnModel::with(['orderItem.product', 'orderItem.order.user']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by reason
        if ($request->has('reason') && $request->reason !== 'all') {
            $query->where('reason', $request->reason);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('orderItem.order.user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total_returns' => ReturnModel::count(),
            'pending_returns' => ReturnModel::where('status', 'requested')->count(),
            'approved_returns' => ReturnModel::where('status', 'approved')->count(),
            'completed_returns' => ReturnModel::where('status', 'completed')->count(),
            'common_reasons' => ReturnModel::select('reason', DB::raw('count(*) as count'))
                ->groupBy('reason')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        return view('admin.returns.index', compact('returns', 'stats'));
    }

    /**
     * Admin: View return details
     */
    public function adminShow(ReturnModel $return)
    {
        $this->authorize('admin');

        $return->load([
            'orderItem.product.productSizes',
            'orderItem.order.user'
        ]);

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Admin: Approve return request
     */
    public function approve(Request $request, ReturnModel $return)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $return->update([
                'status' => 'approved',
                'approved_at' => now(),
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            // Restore stock
            $orderItem = $return->orderItem;
            $product = $orderItem->product;
            $productSize = $product->productSizes()
                ->where('size', $orderItem->size)
                ->first();
            
            if ($productSize) {
                $productSize->increment('stock_quantity', $orderItem->quantity);
            }
            $product->increment('stock_quantity', $orderItem->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return approved successfully',
                'return' => $return->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve return: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin: Reject return request
     */
    public function reject(Request $request, ReturnModel $return)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $return->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return rejected',
            'return' => $return->fresh(),
        ]);
    }

    /**
     * Admin: Mark return as completed
     */
    public function complete(ReturnModel $return)
    {
        $this->authorize('admin');

        if ($return->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Can only complete approved returns',
            ], 400);
        }

        $return->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return marked as completed',
            'return' => $return->fresh(),
        ]);
    }

    /**
     * Get return analytics
     */
    public function analytics(Request $request)
    {
        $this->authorize('admin');

        $period = $request->get('period', 'month');
        
        $startDate = match($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        // Return rate by category
        $returnsByCategory = DB::table('returns')
            ->join('order_items', 'returns.order_item_id', '=', 'order_items.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('returns.created_at', '>=', $startDate)
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.name')
            ->get();

        // Return reasons distribution
        $returnReasons = ReturnModel::where('created_at', '>=', $startDate)
            ->select('reason', DB::raw('count(*) as count'))
            ->groupBy('reason')
            ->get();

        // Size-related returns
        $sizeIssues = ReturnModel::whereIn('reason', ['too_small', 'too_large'])
            ->where('created_at', '>=', $startDate)
            ->count();

        $totalReturns = ReturnModel::where('created_at', '>=', $startDate)->count();
        $sizeIssuePercentage = $totalReturns > 0 ? ($sizeIssues / $totalReturns) * 100 : 0;

        return response()->json([
            'success' => true,
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'analytics' => [
                'total_returns' => $totalReturns,
                'size_issue_percentage' => round($sizeIssuePercentage, 2),
                'returns_by_category' => $returnsByCategory,
                'return_reasons' => $returnReasons,
            ],
        ]);
    }

    /**
     * Get return statistics for dashboard
     */
    public function statistics()
    {
        $this->authorize('admin');

        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'today' => [
                'total' => ReturnModel::whereDate('created_at', $today)->count(),
                'pending' => ReturnModel::whereDate('created_at', $today)->where('status', 'requested')->count(),
            ],
            'this_week' => [
                'total' => ReturnModel::where('created_at', '>=', $thisWeek)->count(),
                'size_related' => ReturnModel::where('created_at', '>=', $thisWeek)
                    ->whereIn('reason', ['too_small', 'too_large'])->count(),
            ],
            'this_month' => [
                'total' => ReturnModel::where('created_at', '>=', $thisMonth)->count(),
                'approved' => ReturnModel::where('created_at', '>=', $thisMonth)
                    ->where('status', 'approved')->count(),
                'completed' => ReturnModel::where('created_at', '>=', $thisMonth)
                    ->where('status', 'completed')->count(),
            ],
            'top_reasons' => ReturnModel::select('reason', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('reason')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats,
        ]);
    }

    /**
     * Generate unique return number
     */
    private function generateReturnNumber(): string
    {
        do {
            $returnNumber = 'RET-' . strtoupper(Str::random(8));
        } while (ReturnModel::where('return_number', $returnNumber)->exists());

        return $returnNumber;
    }

    /**
     * Export returns to CSV (Admin)
     */
    public function export(Request $request)
    {
        $this->authorize('admin');

        $returns = ReturnModel::with(['orderItem.product', 'orderItem.order.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'returns_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($returns) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Return Number',
                'Order Number',
                'Customer Name',
                'Product',
                'Size Returned',
                'Reason',
                'Status',
                'Date Requested',
                'Date Completed',
            ]);

            // Data
            foreach ($returns as $return) {
                fputcsv($file, [
                    $return->return_number,
                    $return->orderItem->order->order_number,
                    $return->orderItem->order->user->name,
                    $return->orderItem->product->name,
                    $return->returned_size,
                    $return->reason,
                    $return->status,
                    $return->created_at->format('Y-m-d H:i:s'),
                    $return->completed_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
