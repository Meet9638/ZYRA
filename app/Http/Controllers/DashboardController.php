<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use App\Models\SizeRecommendation;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // No dependencies
    }

    /**
     * Display admin dashboard
     */
    public function index(Request $request)
    {
        // $this->authorize('admin');

        $period = $request->get('period', 'week');
        $startDate = $this->getStartDate($period);

        // Key Performance Indicators
        $kpis = $this->getKPIs($startDate);

        // Revenue trends
        $revenueTrend = $this->getRevenueTrend($period);

        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent activities
        $recentActivities = $this->getRecentActivities();


        // Top products
        $topProducts = $this->getTopProducts($startDate);

        // Category performance
        $categoryPerformance = $this->getCategoryPerformance($startDate);

        return view('admin.dashboard', compact(
            'kpis',
            'revenueTrend',
            'recentOrders',
            'recentActivities',
            'topProducts',
            'categoryPerformance'
        ));
    }

    /**
     * Get Key Performance Indicators
     */
    private function getKPIs($startDate): array
    {
        $currentPeriodOrders = Order::where('created_at', '>=', $startDate);
        $previousStartDate = $this->getPreviousPeriodStart($startDate);
        $previousPeriodOrders = Order::whereBetween('created_at', [
            $previousStartDate,
            $startDate
        ]);

        // Revenue
        $currentRevenue = $currentPeriodOrders->clone()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total_amount');
        
        $previousRevenue = $previousPeriodOrders->clone()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total_amount');

        $revenueChange = $previousRevenue > 0 
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;

        // Orders
        $currentOrders = $currentPeriodOrders->clone()->count();
        $previousOrders = $previousPeriodOrders->clone()->count();
        $ordersChange = $previousOrders > 0 
            ? (($currentOrders - $previousOrders) / $previousOrders) * 100 
            : 0;

        // Return Rate
        $currentReturns = ReturnModel::where('created_at', '>=', $startDate)->count();
        $returnRate = $currentOrders > 0 ? ($currentReturns / $currentOrders) * 100 : 0;

        $previousReturns = ReturnModel::whereBetween('created_at', [
            $previousStartDate,
            $startDate
        ])->count();
        $previousReturnRate = $previousOrders > 0 ? ($previousReturns / $previousOrders) * 100 : 0;
        $returnRateChange = $previousReturnRate > 0 
            ? (($returnRate - $previousReturnRate) / $previousReturnRate) * 100 
            : 0;


        return [
            'revenue' => [
                'value' => $currentRevenue,
                'change' => round($revenueChange, 2),
                'direction' => $revenueChange >= 0 ? 'up' : 'down',
            ],
            'orders' => [
                'value' => $currentOrders,
                'change' => round($ordersChange, 2),
                'direction' => $ordersChange >= 0 ? 'up' : 'down',
            ],
            'return_rate' => [
                'value' => round($returnRate, 2),
                'change' => round($returnRateChange, 2),
                'direction' => $returnRateChange <= 0 ? 'up' : 'down', // Lower is better
            ],
        ];
    }

    /**
     * Get revenue trend data
     */
    private function getRevenueTrend($period): array
    {
        $days = match($period) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 7,
        };

        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $revenue = Order::whereDate('created_at', $date)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total_amount');

            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue,
            ];
        }

        return $trend;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'type' => 'order',
                'icon' => '🛍️',
                'title' => "New order placed - {$order->order_number}",
                'description' => "by {$order->user->name}",
                'time' => $order->created_at,
            ];
        }

        // Recent returns
        $recentReturns = ReturnModel::with(['orderItem.product', 'orderItem.order.user'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentReturns as $return) {
            $activities[] = [
                'type' => 'return',
                'icon' => '↩️',
                'title' => "Return requested - {$return->orderItem->product->name}",
                'description' => "Reason: {$return->reason}",
                'time' => $return->created_at,
            ];
        }

        // Recent users
        $newUsers = User::orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        foreach ($newUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'icon' => '👤',
                'title' => "New customer registered",
                'description' => $user->name,
                'time' => $user->created_at,
            ];
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get top selling products
     */
    private function getTopProducts($startDate, $limit = 5): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereNotIn('orders.status', ['cancelled', 'refunded'])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get category performance
     */
    private function getCategoryPerformance($startDate): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereNotIn('orders.status', ['cancelled', 'refunded'])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as orders'),
                DB::raw('SUM(order_items.quantity) as items_sold')
            )
            ->groupBy('categories.name')
            ->orderBy('revenue', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get analytics overview
     */
    public function analytics(Request $request)
    {
        $this->authorize('admin');

        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        $analytics = [
            'overview' => [
                'total_revenue' => Order::whereNotIn('status', ['cancelled', 'refunded'])
                    ->sum('total_amount'),
                'total_orders' => Order::count(),
                'total_customers' => User::count(),
                'total_products' => Product::count(),
            ],
            'sales' => [
                'period_revenue' => Order::where('created_at', '>=', $startDate)
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->sum('total_amount'),
                'period_orders' => Order::where('created_at', '>=', $startDate)->count(),
                'average_order_value' => Order::where('created_at', '>=', $startDate)
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->avg('total_amount'),
            ],
            'customers' => [
                'new_customers' => User::where('created_at', '>=', $startDate)->count(),
                'returning_customers' => $this->getReturningCustomers($startDate),
                'top_customers' => $this->getTopCustomers($startDate, 5),
            ],
            'products' => [
                'top_selling' => $this->getTopProducts($startDate, 10),
                'low_stock' => Product::where('stock_quantity', '<', 10)
                    ->where('is_active', true)
                    ->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)
                    ->where('is_active', true)
                    ->count(),
            ],
        ];

        return view('admin.analytics', compact('period', 'startDate', 'analytics'));
    }

    /**
     * Get sales report
     */
    public function salesReport(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $orders = Order::whereBetween('created_at', [
                $validated['start_date'],
                $validated['end_date']
            ])
            ->with(['user', 'orderItems.product'])
            ->get();

        $report = [
            'period' => [
                'start' => $validated['start_date'],
                'end' => $validated['end_date'],
            ],
            'summary' => [
                'total_orders' => $orders->count(),
                'total_revenue' => $orders->whereNotIn('status', ['cancelled', 'refunded'])
                    ->sum('total_amount'),
                'total_items_sold' => $orders->sum(function ($order) {
                    return $order->orderItems->sum('quantity');
                }),
                'average_order_value' => $orders->whereNotIn('status', ['cancelled', 'refunded'])
                    ->avg('total_amount'),
            ],
            'by_status' => $orders->groupBy('status')->map->count(),
            'by_day' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($dayOrders) {
                return [
                    'count' => $dayOrders->count(),
                    'revenue' => $dayOrders->whereNotIn('status', ['cancelled', 'refunded'])
                        ->sum('total_amount'),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'report' => $report,
        ]);
    }

    /**
     * Helper: Get start date based on period
     */
    private function getStartDate($period): Carbon
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfWeek()
        };
    }

    /**
     * Helper: Get previous period start date
     */
    private function getPreviousPeriodStart($currentStart): Carbon
    {
        $duration = now()->diffInDays($currentStart);
        return Carbon::parse($currentStart)->subDays($duration);
    }


    /**
     * Get returning customers count
     */
    private function getReturningCustomers($startDate): int
    {
        return User::whereHas('orders', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })
        ->whereHas('orders', function ($query) use ($startDate) {
            $query->where('created_at', '<', $startDate);
        })
        ->count();
    }

    /**
     * Get top customers by spending
     */
    private function getTopCustomers($startDate, $limit = 5): array
    {
        return DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereNotIn('orders.status', ['cancelled', 'refunded'])
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
