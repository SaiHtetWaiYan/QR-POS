<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'total_amount',
        'coupon_value',
        'total_codes',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'max_uses_per_code',
        'code_prefix',
        'code_length',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'coupon_value' => 'decimal:2',
    ];

    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function coupons()
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function generateCoupons(): void
    {
        if (! $this->total_amount || ! $this->coupon_value) {
            return;
        }

        $totalCoupons = (int) floor($this->total_amount / $this->coupon_value);
        $codes = [];
        $existingCodes = DiscountCode::pluck('code')->toArray();
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        for ($i = 0; $i < $totalCoupons; $i++) {
            do {
                $code = '';
                for ($j = 0; $j < 8; $j++) {
                    $code .= $characters[random_int(0, strlen($characters) - 1)];
                }
            } while (in_array($code, $existingCodes) || in_array($code, array_column($codes, 'code')));

            $codes[] = [
                'coupon_campaign_id' => $this->id,
                'code' => $code,
                'type' => 'fixed',
                'value' => $this->coupon_value,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'max_uses' => 1,
                'uses_count' => 0,
                'is_active' => true,
                'status' => 'unused',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($codes) > 0) {
            DiscountCode::insert($codes);
            $this->update(['total_codes' => count($codes)]);
        }
    }

    public function getUsedCouponsCountAttribute(): int
    {
        return $this->discountCodes()->where('status', 'used')->count();
    }

    public function getUnusedCouponsCountAttribute(): int
    {
        return $this->discountCodes()->where('status', 'unused')->count();
    }

    public function getExpiredCouponsCountAttribute(): int
    {
        return $this->discountCodes()->where('status', 'expired')->count();
    }

    public function getDisabledCouponsCountAttribute(): int
    {
        return $this->discountCodes()->where('status', 'disabled')->count();
    }
}
