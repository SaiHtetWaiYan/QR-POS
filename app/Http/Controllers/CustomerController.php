<?php

namespace App\Http\Controllers;

use App\Events\BillRequested;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    private function getTable($code)
    {
        return Table::where('code', $code)->where('is_active', true)->firstOrFail();
    }

    public function index($tableCode)
    {
        $table = $this->getTable($tableCode);
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('is_available', true);
        }])->orderBy('sort_order')->get();

        // Check for active order in session or DB
        $activeOrder = Order::where('table_id', $table->id)
            ->whereIn('status', ['pending', 'accepted', 'preparing', 'served'])
            ->latest()
            ->first();

        return view('customer.index', compact('table', 'categories', 'activeOrder'));
    }

    public function addToCart(Request $request, $tableCode)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $cart = session()->get('cart', []);
        $itemId = $request->menu_item_id;

        // Simple cart structure: itemId -> details
        // If item exists, update qty (or append if different note? keeping simple: separate lines usually better but let's just add)
        // MVP: Unique by ID. If note changes, maybe just overwrite note.

        $menuItem = MenuItem::find($itemId);

        $existingLineId = null;
        $lineQty = $request->qty;
        $action = 'added';
        foreach ($cart as $lineId => $line) {
            if ($line['menu_item_id'] === $itemId && ($line['note'] ?? null) === ($request->note ?? null)) {
                $existingLineId = $lineId;
                break;
            }
        }

        if ($existingLineId) {
            $cart[$existingLineId]['qty'] += $request->qty;
            $lineQty = $cart[$existingLineId]['qty'];
            $action = 'updated';
        } else {
            // Unique line ID for different notes or items
            $cartLineId = $itemId.'-'.Str::random(4);
            $cart[$cartLineId] = [
                'menu_item_id' => $itemId,
                'qty' => $request->qty,
                'note' => $request->note,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
            ];
        }

        session()->put('cart', $cart);
        $cartCount = collect($cart)->sum(fn ($item) => $item['qty']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $action === 'updated' ? 'Quantity updated' : 'Item added',
                'cart_count' => $cartCount,
                'action' => $action,
                'line_qty' => $lineQty,
                'item_name' => $menuItem->name,
            ]);
        }

        return back()->with('success', 'Added to cart');
    }

    public function viewCart($tableCode)
    {
        $table = $this->getTable($tableCode);
        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);

        return view('customer.cart', compact('table', 'cart', 'subtotal'));
    }

    public function removeItem($tableCode, $lineId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$lineId]);
        session()->put('cart', $cart);

        if (request()->expectsJson()) {
            $cartCount = collect($cart)->sum(fn ($item) => $item['qty']);
            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
            ]);
        }

        return back();
    }

    public function updateCartItem(Request $request, $tableCode, $lineId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        if (!isset($cart[$lineId])) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $cart[$lineId]['qty'] = $request->qty;
        session()->put('cart', $cart);

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);
        $taxRate = config('pos.tax_rate', 0);
        $serviceChargeRate = config('pos.service_charge', 0);
        $tax = $subtotal * $taxRate;
        $serviceCharge = $subtotal * $serviceChargeRate;
        $total = $subtotal + $tax + $serviceCharge;

        $cartCount = collect($cart)->sum(fn ($item) => $item['qty']);

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'qty' => $cart[$lineId]['qty'],
            'line_total' => $cart[$lineId]['price'] * $cart[$lineId]['qty'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'service' => $serviceCharge,
            'total' => $total,
        ]);
    }

    public function placeOrder(Request $request, $tableCode)
    {
        $table = $this->getTable($tableCode);
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
            }

            return back()->with('error', 'Cart is empty');
        }

        // Prevent multiple active orders
        $existingOrder = Order::where('table_id', $table->id)
            ->whereIn('status', ['pending', 'accepted', 'preparing', 'served'])
            ->first();

        if ($existingOrder) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active order. Please ask waiter to add items.',
                    'redirect' => route('customer.status', $tableCode),
                ], 400);
            }

            return redirect()->route('customer.status', $tableCode)->with('error', 'You already have an active order. Please ask waiter to add items.');
        }

        try {
            DB::beginTransaction();

            $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);
            $taxRate = config('pos.tax_rate', 0);
            $serviceChargeRate = config('pos.service_charge', 0);

            $tax = $subtotal * $taxRate;
            $serviceCharge = $subtotal * $serviceChargeRate;
            $total = $subtotal + $tax + $serviceCharge;

            $order = Order::create([
                'table_id' => $table->id,
                'order_no' => 'ORD-'.strtoupper(Str::random(6)),
                'status' => 'pending',
                'customer_note' => $request->customer_note,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'service_charge' => $serviceCharge,
                'total' => $total,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'name_snapshot' => $item['name'],
                    'price_snapshot' => $item['price'],
                    'qty' => $item['qty'],
                    'note' => $item['note'],
                    'line_total' => $item['price'] * $item['qty'],
                ]);
            }

            DB::commit();
            session()->forget('cart');

            // Dispatch event but don't let broadcast failure break the order
            try {
                \App\Events\OrderPlaced::dispatch($order);
            } catch (\Exception $e) {
                // Log broadcast failure but don't fail the order
                \Log::warning('Failed to broadcast OrderPlaced event: '.$e->getMessage());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'redirect' => route('customer.status', $tableCode),
                ]);
            }

            return redirect()->route('customer.status', $tableCode);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Order failed: '.$e->getMessage()], 500);
            }

            return back()->with('error', 'Order failed: '.$e->getMessage());
        }
    }

    public function status($tableCode)
    {
        $table = $this->getTable($tableCode);
        $order = Order::with('orderItems')->where('table_id', $table->id)
            ->whereIn('status', ['pending', 'accepted', 'preparing', 'served', 'cancelled'])
            ->latest()
            ->first();

        return view('customer.status', compact('table', 'order'));
    }

    public function requestBill(Request $request, $tableCode, Order $order)
    {
        if ($order->status === 'paid') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Order already paid'], 400);
            }

            return back();
        }

        $order->update(['bill_requested_at' => now()]);

        // Broadcast to POS
        try {
            BillRequested::dispatch($order->load('table'));
        } catch (\Exception $e) {
            \Log::warning('Failed to broadcast BillRequested: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Bill requested. The waiter is coming!']);
        }

        return back()->with('success', 'Bill requested. The waiter is coming!');
    }
}
