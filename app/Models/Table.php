<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables'; // Explicit table name

    protected $fillable = ['name', 'code', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}