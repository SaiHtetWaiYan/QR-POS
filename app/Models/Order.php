<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'order_no',
        'status',
        'customer_note',
        'subtotal',
        'tax',
        'service_charge',
        'coupon_code_id',
        'coupon_type',
        'coupon_value',
        'coupon_amount',
        'total',
        'payment_method',
        'bill_requested_at',
        'paid_at',
    ];

    protected $casts = [
        'bill_requested_at' => 'datetime',
        'paid_at' => 'datetime',
        'coupon_value' => 'decimal:2',
        'coupon_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }
}
