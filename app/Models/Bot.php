<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    protected $fillable = [
        'maxAmount',
        'percentageAlert',
        'user_id',
        'isAlertSent',
        'minAmount',
    ];

    protected $casts = [
        'isAlertSent' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($bot) {
            if (!$bot->exists || $bot->isDirty('percentageAlert'))
                $bot->minAmount = (int) $bot->maxAmount - ($bot->maxAmount * ($bot->percentageAlert / 100));

            return true;
        });
    }
}
