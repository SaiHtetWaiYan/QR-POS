<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $codes = DiscountCode::whereNull('coupon_campaign_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pos.discounts', compact('codes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['type'] === 'percent') {
            $data['value'] = min($data['value'], 100);
        }

        DiscountCode::create($data);

        return back()->with('success', 'Discount code created');
    }

    public function toggle(DiscountCode $discount)
    {
        $discount->update(['is_active' => ! $discount->is_active]);

        return back()->with('success', 'Discount status updated');
    }

    public function destroy(DiscountCode $discount)
    {
        $discount->delete();

        return back()->with('success', 'Discount code deleted');
    }

    public function disable(DiscountCode $discountCode)
    {
        if ($discountCode->status === 'unused') {
            $discountCode->update(['status' => 'disabled', 'is_active' => false]);
        }

        return back()->with('success', 'Coupon disabled.');
    }

    public function enable(DiscountCode $discountCode)
    {
        if ($discountCode->status === 'disabled') {
            $discountCode->update(['status' => 'unused', 'is_active' => true]);
        }

        return back()->with('success', 'Coupon enabled.');
    }

    public function destroyCode(DiscountCode $discountCode)
    {
        $discountCode->delete();

        return back()->with('success', 'Coupon deleted.');
    }
}
