<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'startingPrice',
        'auction_closes_at',
        'auction_closed_job_id',
    ];

    protected $casts = [
        'auction_closes_at' => 'datetime',
    ];

    protected $hidden = [
        'startingPrice',
        'highestBid',
        'auctionClosedJob',
        'bid_username',
    ];

    protected $appends = [
        'price',
        'bid_user_id'
    ];

    protected $with = ['highestBid'];

    protected function price(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!array_key_exists('highestBid', $this->relations)) $this->load('highestBid');

                $related = $this->getRelation('highestBid');

                return ($related) ? $related->amount : $attributes['startingPrice'];
            },
        );
    }

    protected function bidUserId(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!array_key_exists('highestBid', $this->relations)) $this->load('highestBid');

                $related = $this->getRelation('highestBid');

                return ($related) ? $related->user_id : null;
            },
        );
    }
    protected function bidUsername(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!array_key_exists('highestBid', $this->relations)) $this->load('highestBid');

                $related = $this->getRelation('highestBid');

                return ($related) ? $related->user->username : null;
            },
        );
    }

    protected function billUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (Carbon::parse($this->auction_closes_at)->greaterThan(now()) || $this->bid_user_id == null)
                    return null;

                return
                    auth()->user()->isAdmin || $this->bid_user_id == auth()->user()->id ?
                    URL::temporarySignedRoute('items.bill.show', now()->addMinutes(30), ['id' => $this->id])
                    : null;
            },
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

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function auctionClosedJob(): HasOne
    {
        return $this->hasOne(DelayedJob::class, 'id', 'auction_closed_job_id');
    }

    public function highestBid()
    {
        return $this->hasOne(Bid::class)->latest('amount');
    }
}
