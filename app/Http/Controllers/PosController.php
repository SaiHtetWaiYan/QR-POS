<?php

namespace App\Http\Controllers;

use App\Events\BillRequested;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class PosController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $orders = Order::with(['table', 'orderItems'])
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled') // Keep paid for a bit or filter in view?
            ->orderByRaw("CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'accepted' THEN 2 
                WHEN 'preparing' THEN 3 
                WHEN 'served' THEN 4 
                WHEN 'paid' THEN 5 
                ELSE 6 END")
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate by status for Kanban or Lists
        $pendingAll = $orders->where('status', 'pending');
        $activeAll = $orders->whereIn('status', ['accepted', 'preparing', 'served']);
        $completedAll = $orders->where('status', 'paid'); // or recently paid

        $pending = $pendingAll->take(20);
        $active = $activeAll->take(20);
        $completed = $completedAll->take(20);

        $pendingCount = $pendingAll->count();
        $activeCount = $activeAll->count();
        $completedCount = $completedAll->count();

        $topItems = OrderItem::selectRaw('order_items.menu_item_id, order_items.name_snapshot, SUM(order_items.qty) as total_qty, menu_items.image_path')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('order_items.menu_item_id', 'order_items.name_snapshot', 'menu_items.image_path')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        return view('pos.index', compact('orders', 'pending', 'active', 'completed', 'pendingCount', 'activeCount', 'completedCount', 'topItems'));
    }

    public function history(Request $request)
    {
        $dateParam = $request->query('date');
        $date = $dateParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateParam)
            ? Carbon::parse($dateParam)->toDateString()
            : now()->toDateString();

        $orders = Order::with(['table', 'orderItems'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $availableDates = Order::selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');

        return view('pos.history', compact('orders', 'date', 'availableDates', 'totalOrders', 'totalRevenue'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems', 'table']);

        return view('pos.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $paymentMethods = config('pos.payment_methods', []);
        $request->validate([
            'status' => 'required|in:pending,accepted,preparing,served,paid,cancelled',
            'payment_method' => ['nullable', Rule::in($paymentMethods)],
        ]);

        $data = ['status' => $request->status];
        if ($request->status === 'paid') {
            $data['paid_at'] = now();
            $selectedMethod = $request->input('payment_method')
                ?? $order->bill_payment_method
                ?? ($paymentMethods[0] ?? null);
            if ($selectedMethod) {
                $data['payment_method'] = $selectedMethod;
            }
        }

        $order->update($data);

        if ($request->status === 'accepted' && $order->coupon_code_id) {
            $couponCode = $order->couponCode;
            if ($couponCode && $couponCode->status === 'unused') {
                $couponCode->markAsUsed();
            }
        }

        // Broadcast status update to customer
        try {
            OrderStatusUpdated::dispatch($order);
        } catch (\Exception $e) {
            \Log::warning('Failed to broadcast OrderStatusUpdated: '.$e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'order_id' => $order->id,
            ]);
        }

        return back()->with('success', 'Order updated');
    }

    public function reports()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $dailyOrders = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled');
        $monthlyOrders = Order::whereBetween('created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->where('status', '!=', 'cancelled');

        $dailyCount = $dailyOrders->count();
        $monthlyCount = $monthlyOrders->count();
        $dailyRevenue = $dailyOrders->sum('total');
        $monthlyRevenue = $monthlyOrders->sum('total');
        $monthlyItemsSold = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->sum('order_items.qty');
        $avgOrderValue = $monthlyCount > 0 ? $monthlyRevenue / $monthlyCount : 0;

        $monthlyTotalCount = Order::whereBetween('created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])->count();
        $monthlyCancelledCount = Order::whereBetween('created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->where('status', 'cancelled')
            ->count();
        $monthlyCancelRate = $monthlyTotalCount > 0 ? ($monthlyCancelledCount / $monthlyTotalCount) * 100 : 0;

        $trendStart = now()->subDays(13)->startOfDay();
        $trendEnd = now()->endOfDay();
        $trendRows = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(total) as revenue')
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $trend = [];
        $trendMaxOrders = 1;
        $trendMaxRevenue = 1;
        foreach (CarbonPeriod::create($trendStart->toDateString(), $trendEnd->toDateString()) as $date) {
            $dateKey = $date->toDateString();
            $row = $trendRows->get($dateKey);
            $ordersCount = (int) ($row->orders_count ?? 0);
            $revenue = (float) ($row->revenue ?? 0);
            $trendMaxOrders = max($trendMaxOrders, $ordersCount);
            $trendMaxRevenue = max($trendMaxRevenue, $revenue);
            $trend[] = [
                'date' => $dateKey,
                'label' => $date->format('M j'),
                'orders' => $ordersCount,
                'revenue' => $revenue,
            ];
        }

        $hourRows = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as orders_count')
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->where('status', '!=', 'cancelled')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        $hourly = [];
        $hourMax = 1;
        for ($hour = 0; $hour < 24; $hour++) {
            $count = (int) ($hourRows->get($hour)->orders_count ?? 0);
            $hourMax = max($hourMax, $count);
            $hourly[] = [
                'hour' => $hour,
                'label' => Carbon::createFromTime($hour)->format('ga'),
                'orders' => $count,
            ];
        }

        $busiestHour = collect($hourly)->sortByDesc('orders')->first();
        $busiestHourLabel = $busiestHour['label'] ?? '—';
        $busiestHourOrders = $busiestHour['orders'] ?? 0;

        $statusRows = Order::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        $statusBreakdown = [];
        foreach (['pending', 'accepted', 'preparing', 'served', 'paid', 'cancelled'] as $status) {
            $statusBreakdown[] = [
                'status' => $status,
                'count' => (int) ($statusRows->get($status)->count ?? 0),
            ];
        }

        $topItems = OrderItem::selectRaw('name_snapshot, SUM(qty) as total_qty, SUM(line_total) as total_revenue')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('name_snapshot')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        $recentOrders = Order::with('table')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        return view('pos.reports', compact(
            'dailyCount',
            'monthlyCount',
            'dailyRevenue',
            'monthlyRevenue',
            'monthlyItemsSold',
            'avgOrderValue',
            'monthlyTotalCount',
            'monthlyCancelledCount',
            'monthlyCancelRate',
            'today',
            'trend',
            'trendMaxOrders',
            'trendMaxRevenue',
            'hourly',
            'hourMax',
            'busiestHourLabel',
            'busiestHourOrders',
            'statusBreakdown',
            'topItems',
            'recentOrders'
        ));
    }

    public function exportReports(string $range)
    {
        $range = strtolower($range);
        if ($range === 'last14') {
            $start = now()->subDays(13)->startOfDay();
            $end = now()->endOfDay();
            $label = 'last-14-days';
        } elseif ($range === 'month') {
            $start = now()->startOfMonth()->startOfDay();
            $end = now()->endOfMonth()->endOfDay();
            $label = now()->format('Y-m');
        } else {
            abort(404);
        }

        $rows = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(total) as revenue')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $filename = 'pos-report-'.$label.'.csv';

        return response()->streamDownload(function () use ($start, $end, $rows) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['date', 'orders', 'revenue']);

            foreach (CarbonPeriod::create($start->toDateString(), $end->toDateString()) as $date) {
                $dateKey = $date->toDateString();
                $row = $rows->get($dateKey);
                $ordersCount = (int) ($row->orders_count ?? 0);
                $revenue = number_format((float) ($row->revenue ?? 0), 2, '.', '');
                fputcsv($handle, [$dateKey, $ordersCount, $revenue]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function print(Order $order)
    {
        $order->load(['orderItems', 'table']);

        return view('pos.print', compact('order'));
    }

    public function orderCard(Order $order)
    {
        $order->load(['orderItems.menuItem', 'table']);

        return view('pos.partials.order_card', compact('order'));
    }

    public function reAlertBill(Order $order)
    {
        if (! $order->bill_requested_at || $order->status === 'paid') {
            return back()->with('error', __('No bill request pending for this order.'));
        }

        try {
            BillRequested::dispatch($order->load('table'));
        } catch (\Exception $e) {
            \Log::warning('Failed to broadcast BillRequested: '.$e->getMessage());

            return back()->with('error', __('Failed to send alert. Please try again.'));
        }

        return back()->with('success', __('Bill alert sent to all POS screens.'));
    }
}
