@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div   class="auto-style-0009">
    <!-- Revenue Card -->
    <div   class="auto-style-0010">
        <div   class="auto-style-0011"></div>
        <div   class="auto-style-0012">
            <div   class="auto-style-0013">💰</div>
            <div   class="auto-style-0014">
                ↑ {{ number_format($kpis['revenue']['change'], 1) }}%
            </div>
        </div>
        <div class="auto-style-0015">
            &#8377;{{ number_format($kpis['revenue']['value'], 2) }}
        </div>
        <div   class="auto-style-0016">
            Total Revenue
        </div>
    </div>

    <!-- Orders Card -->
    <div   class="auto-style-0010">
        <div   class="auto-style-0011"></div>
        <div   class="auto-style-0012">
            <div   class="auto-style-0013">📦</div>
            <div   class="auto-style-0014">
                ↑ {{ number_format($kpis['orders']['change'], 1) }}%
            </div>
        </div>
        <div   class="auto-style-0015">
            {{ number_format($kpis['orders']['value']) }}
        </div>
        <div   class="auto-style-0016">
            Total Orders
        </div>
    </div>

    <!-- Return Rate Card -->
    <div   class="auto-style-0010">
        <div   class="auto-style-0011"></div>
        <div   class="auto-style-0012">
            <div   class="auto-style-0013">↩️</div>
            <div   class="auto-style-0014">
                ↓ {{ number_format(abs($kpis['return_rate']['change']), 1) }}%
            </div>
        </div>
        <div   class="auto-style-0015">
            {{ number_format($kpis['return_rate']['value'], 1) }}%
        </div>
        <div   class="auto-style-0016">
            Return Rate
        </div>
    </div>

</div>


<!-- Charts and Recent Activity -->
<div   class="auto-style-0024">
    <!-- Revenue Trend Chart -->
    <div class="auto-style-0025">
        <h3 class="auto-style-0026">Revenue Trend</h3>
        <div class="auto-style-0027" style="height: 300px; display: block; align-items: stretch; justify-content: stretch; background: transparent; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div   class="auto-style-0025">
        <h3   class="auto-style-0026">Recent Activity</h3>
        <div>
            @foreach($recentActivities as $activity)
                <div   class="auto-style-0030">
                    <div   class="auto-style-0031">
                        {{ $activity['icon'] }}
                    </div>
                    <div   class="auto-style-0032">
                        <div   class="auto-style-0033">{{ $activity['title'] }}</div>
                        <div   class="auto-style-0034">{{ $activity['time']->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div   class="auto-style-0035">
    <div   class="auto-style-0036">
        <h3   class="auto-style-0037">Recent Orders</h3>
    </div>
    <table   class="auto-style-0038">
        <thead   class="auto-style-0039">
            <tr>
                <th   class="auto-style-0040">Order ID</th>
                <th   class="auto-style-0040">Customer</th>
                <th   class="auto-style-0040">Items</th>
                <th   class="auto-style-0040">Status</th>
                <th   class="auto-style-0040">Amount</th>
                <th   class="auto-style-0040">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr class="auto-style-0041" onclick="window.location='{{ route('admin.orders.show', $order->id) }}'" onmouseover="this.style.background='rgba(212, 175, 55, 0.05)'" onmouseout="this.style.background='transparent'">
                    <td   class="auto-style-0042">
                        <strong>{{ $order->order_number }}</strong>
                    </td>
                    <td   class="auto-style-0042">
                        {{ $order->user->name }}
                    </td>
                    <td   class="auto-style-0042">
                        {{ $order->orderItems->count() }} item(s)
                    </td>
                    <td   class="auto-style-0042">
                        @php
                            $statusColors = [
                                'pending' => 'var(--warning)',
                                'processing' => 'var(--zyra-gold)',
                                'shipped' => '#3b82f6',
                                'delivered' => 'var(--success)',
                                'cancelled' => 'var(--danger)',
                                'refunded' => 'var(--danger)'
                            ];
                            $statusColor = $statusColors[$order->status] ?? 'var(--zyra-silver)';
                        @endphp
                        <span style="padding: 0.4rem 0.8rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; background: {{ $statusColor }}20; color: {{ $statusColor }};">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="auto-style-0044">
                        ₹{{ number_format($order->total_amount, 2) }}
                    </td>
                    <td   class="auto-style-0045">
                        {{ $order->created_at->format('M d, Y') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Top Products -->
<div   class="auto-style-0025">
    <h3   class="auto-style-0026">Top Selling Products</h3>
    <div   class="auto-style-0046">
        @foreach($topProducts as $product)
            <div   class="auto-style-0047">
                <div>
                    <div   class="auto-style-0048">{{ $product->name }}</div>
                    <div   class="auto-style-0049">{{ $product->total_sold }} units sold</div>
                </div>
                <div class="auto-style-0050">
                    <div class="auto-style-0051">&#8377;{{ number_format($product->total_revenue, 2) }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartDataRaw = @json($revenueTrend);
    
    // Sort data chronologically if needed, but assuming it comes ordered or needs reverse
    // The trend is built backwards in controller (from days-1 down to 0) so it's already chronological
    const labels = chartDataRaw.map(item => item.date);
    const data = chartDataRaw.map(item => parseFloat(item.revenue));

    // Create a beautiful gradient for the chart background
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(212, 175, 55, 0.5)'); // Gold at the top
    gradient.addColorStop(1, 'rgba(212, 175, 55, 0.0)'); // Transparent at the bottom

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: data,
                borderColor: '#d4af37',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4, // Add smooth curves
                pointBackgroundColor: '#121212',
                pointBorderColor: '#d4af37',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#d4af37'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 10000, // Provides a better visual scale for Rupees even with 0 or small values
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false,
                        borderDash: [5, 5] // Make horizontal lines dashed
                    },
                    ticks: {
                        color: '#b0b0b0',
                        font: { size: 11 },
                        padding: 10,
                        precision: 0, // Prevents 0.1, 0.2 from showing up when data is mostly 0
                        callback: function(value) {
                            return '₹' + value;
                        }
                    },
                    border: { display: false }
                },
                x: {
                    grid: {
                        display: false, // Remove vertical grid lines for a cleaner look
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#b0b0b0',
                        font: { size: 11 },
                        padding: 10,
                        maxRotation: 45,
                        minRotation: 45
                    },
                    border: { display: false }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e1e1e',
                    titleColor: '#d4af37',
                    bodyColor: '#ffffff',
                    borderColor: '#2c2c2c',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₹' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>
@endsection
