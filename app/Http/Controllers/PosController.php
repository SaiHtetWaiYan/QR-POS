<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $orders = Order::with(['table', 'orderItems'])
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

        return back()->with('success', 'Order updated');
    }

    public function print(Order $order)
    {
        $order->load(['orderItems', 'table']);
        return view('pos.print', compact('order'));
    }
}