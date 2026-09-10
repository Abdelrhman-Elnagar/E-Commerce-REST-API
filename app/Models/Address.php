<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
     protected $fillable = [
        'title',
        'full_name',
        'phone',
        'address_line',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    //user has many addresses
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
