<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'name_my',
        'description',
        'description_my',
        'price',
        'image_path',
        'is_available',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'my' && $this->name_my) {
            return $this->name_my;
        }

        return $this->name;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'my' && $this->description_my) {
            return $this->description_my;
        }

        return $this->description;
    }
}
