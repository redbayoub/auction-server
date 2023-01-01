<?php

namespace App\Models;

use App\Enums\BidStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // check auction closed
                if (Carbon::parse($this->item->auction_closes_at)->lessThan(now())) {
                    return $this->item->bidUserId  == $this->user_id ? BidStatus::WON : BidStatus::LOST;
                }

                // auction not closed
                return $this->item->bidUserId  == $this->user_id ? BidStatus::IN_PROGRESS : BidStatus::LOST;
            },
        );
    }
}
