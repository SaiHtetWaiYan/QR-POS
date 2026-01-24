<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_my', 'sort_order'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'my' && $this->name_my) {
            return $this->name_my;
        }

        return $this->name;
    }
}
