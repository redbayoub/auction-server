<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'startingPrice',
        'auction_closes_at',
    ];

    protected $casts = [
        'auction_closes_at' => 'datetime',
    ];

    protected $hidden = [
        'startingPrice',
    ];

    protected $appends = [
        'price',
    ];

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['startingPrice'],
        );
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
            filter_var($value, FILTER_VALIDATE_URL) ?
                $value : env('APP_URL') . Storage::url($value),
        );
    }
}
