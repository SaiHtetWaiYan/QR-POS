<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'max_uses_per_code',
        'code_prefix',
        'code_length',
        'total_codes',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function generateCodes(int $count): void
    {
        $codes = [];
        $existingCodes = DiscountCode::pluck('code')->toArray();

        for ($i = 0; $i < $count; $i++) {
            do {
                $code = $this->code_prefix
                    ? strtoupper($this->code_prefix.Str::random($this->code_length - strlen($this->code_prefix)))
                    : strtoupper(Str::random($this->code_length));
            } while (in_array($code, $existingCodes) || in_array($code, array_column($codes, 'code')));

            $codes[] = [
                'coupon_campaign_id' => $this->id,
                'code' => $code,
                'type' => $this->type,
                'value' => $this->value,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'max_uses' => $this->max_uses_per_code,
                'uses_count' => 0,
                'is_active' => true,
                'status' => 'unused',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DiscountCode::insert($codes);
    }

    public function getUsedCodesCountAttribute(): int
    {
        return $this->discountCodes()->where('status', 'used')->count();
    }

    public function getUnusedCodesCountAttribute(): int
    {
        return $this->discountCodes()->where('status', 'unused')->count();
    }
}
