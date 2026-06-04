<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;

use App\Models\User;
use App\Models\OrderItem;
use App\Models\ReturnModel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs for dashboard
        $kpis = [
            'revenue' => ['value' => Order::where('status', '!=', 'cancelled')->sum('total_amount'), 'change' => 5.2],
            'orders' => ['value' => Order::count(), 'change' => 2.1],
            'return_rate' => ['value' => Order::count() > 0 ? (ReturnModel::count() / Order::count()) * 100 : 0, 'change' => -0.5],
        ];

        $recentActivities = [];
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        $topProducts = Product::orderBy('created_at', 'desc')->take(5)->get();
        $revenueTrend = [];

        return view('admin.dashboard', compact('kpis', 'recentActivities', 'recentOrders', 'topProducts', 'revenueTrend'));
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = match($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        $analytics = [
            'overview' => [
                'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            ],
            'sales' => [
                'period_revenue' => Order::where('status', '!=', 'cancelled')
                                       ->where('created_at', '>=', $startDate)
                                       ->sum('total_amount'),
                'period_orders' => Order::where('created_at', '>=', $startDate)->count(),
            ],
            'customers' => [
                'new_customers' => User::where('created_at', '>=', $startDate)->count(),
                'top_customers' => User::withCount(['orders as total_orders'])
                                     ->withSum(['orders as total_spent' => function($q) {
                                         $q->where('status', '!=', 'cancelled');
                                     }], 'total_amount')
                                     ->orderBy('total_spent', 'desc')
                                     ->take(5)
                                     ->get(),
            ],
            'products' => [
                'top_selling' => Product::withCount(['orderItems as total_sold' => function($q) use ($startDate) {
                                            $q->where('created_at', '>=', $startDate);
                                         }])
                                         ->withSum(['orderItems as total_revenue' => function($q) use ($startDate) {
                                            $q->where('created_at', '>=', $startDate);
                                         }], 'total_price')
                                         ->orderBy('total_sold', 'desc')
                                         ->take(5)
                                         ->get(),
                'low_stock' => Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count(),
                'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            ]
        ];

        return view('admin.analytics', compact('analytics', 'period'));
    }
}
?>
