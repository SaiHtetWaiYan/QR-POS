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
        'discount_code_id',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'bill_requested_at',
        'paid_at',
    ];

    protected $casts = [
        'bill_requested_at' => 'datetime',
        'paid_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }
}
