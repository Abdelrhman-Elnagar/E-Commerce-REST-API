<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
        protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'short_description',
        'description',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // public function images(): HasMany
    // {
    //     return $this->hasMany(ProductImage::class);
    // }
}
