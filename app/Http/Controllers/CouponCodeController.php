<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;

class CouponCodeController extends Controller
{
    public function disable(CouponCode $couponCode)
    {
        if ($couponCode->status === 'unused') {
            $couponCode->update(['status' => 'disabled', 'is_active' => false]);
        }

        return back()->with('success', 'Coupon disabled.');
    }

    public function enable(CouponCode $couponCode)
    {
        if ($couponCode->status === 'disabled') {
            $couponCode->update(['status' => 'unused', 'is_active' => true]);
        }

        return back()->with('success', 'Coupon enabled.');
    }

    public function destroyCode(CouponCode $couponCode)
    {
        $couponCode->delete();

        return back()->with('success', 'Coupon deleted.');
    }
}
