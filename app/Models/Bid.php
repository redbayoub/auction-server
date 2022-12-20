<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'user_id',
        'item_id',
        'isBot',
    ];

    protected $casts = [
        'isBot' => 'boolean',
    ];

    protected $hidden = [
        'isBot',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
