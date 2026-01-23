<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        $topItem = OrderItem::selectRaw('name_snapshot, SUM(qty) as total_qty')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('name_snapshot')
            ->orderByDesc('total_qty')
            ->first();

        return view('pos.index', compact('orders', 'pending', 'active', 'completed', 'pendingCount', 'activeCount', 'completedCount', 'topItem'));
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
        $request->validate(['status' => 'required|in:pending,accepted,preparing,served,paid,cancelled']);
        
        $data = ['status' => $request->status];
        if ($request->status === 'paid') {
            $data['paid_at'] = now();
        }

        $order->update($data);

        // Broadcast status update to customer
        try {
            OrderStatusUpdated::dispatch($order);
        } catch (\Exception $e) {
            \Log::warning('Failed to broadcast OrderStatusUpdated: '.$e->getMessage());
        }

        return back()->with('success', 'Order updated');
    }

    public function updateDiscount(Request $request, Order $order)
    {
        $request->validate([
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        $discountType = $request->discount_type;
        $discountValue = $request->discount_value;

        if (!$discountType || !$discountValue) {
            $discountType = null;
            $discountValue = null;
            $discountAmount = 0;
        } else {
            if ($discountType === 'percent') {
                $discountValue = min($discountValue, 100);
                $discountAmount = $order->subtotal * ($discountValue / 100);
            } else {
                $discountAmount = min($discountValue, $order->subtotal);
            }
        }

        $total = max(0, $order->subtotal + $order->tax + $order->service_charge - $discountAmount);

        $order->update([
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ]);

        return back()->with('success', 'Discount updated');
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

        $dailyTopItem = OrderItem::selectRaw('name_snapshot, SUM(qty) as total_qty')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('name_snapshot')
            ->orderByDesc('total_qty')
            ->first();

        $monthlyTopItem = OrderItem::selectRaw('name_snapshot, SUM(qty) as total_qty')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('name_snapshot')
            ->orderByDesc('total_qty')
            ->first();

        return view('pos.reports', compact(
            'dailyCount',
            'monthlyCount',
            'dailyRevenue',
            'monthlyRevenue',
            'dailyTopItem',
            'monthlyTopItem',
            'today'
        ));
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
}
