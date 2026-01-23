<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_campaign_id',
        'code',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'max_uses',
        'uses_count',
        'is_active',
        'status',
        'used_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'used_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function couponCampaign()
    {
        return $this->belongsTo(CouponCampaign::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function isUsable(): bool
    {
        if ($this->status !== 'unused') {
            return false;
        }

        if (! $this->is_active) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->max_uses && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function markAsUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'uses_count' => $this->uses_count + 1,
        ]);
    }

    public function calculateCoupon(float $subtotal): float
    {
        if ($this->type === 'percent') {
            return round($subtotal * ($this->value / 100), 2);
        }

        return min($this->value, $subtotal);
    }

    public function scopeUnused($query)
    {
        return $query->where('status', 'unused');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
