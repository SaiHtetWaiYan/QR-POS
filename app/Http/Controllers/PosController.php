<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
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
        $pending = $orders->where('status', 'pending');
        $active = $orders->whereIn('status', ['accepted', 'preparing', 'served']);
        $completed = $orders->where('status', 'paid'); // or recently paid

        return view('pos.index', compact('orders', 'pending', 'active', 'completed'));
    }

    public function history(Request $request)
    {
        $dateParam = $request->query('date');
        $date = $dateParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateParam)
            ? Carbon::parse($dateParam)->toDateString()
            : now()->toDateString();

        $orders = Order::with(['table', 'orderItems'])
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'cancelled')
            ->orderByRaw("CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'accepted' THEN 2 
                WHEN 'preparing' THEN 3 
                WHEN 'served' THEN 4 
                WHEN 'paid' THEN 5 
                ELSE 6 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $availableDates = Order::selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        $pending = $orders->where('status', 'pending');
        $active = $orders->whereIn('status', ['accepted', 'preparing', 'served']);
        $completed = $orders->where('status', 'paid');

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');

        return view('pos.history', compact('orders', 'pending', 'active', 'completed', 'date', 'availableDates', 'totalOrders', 'totalRevenue'));
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
