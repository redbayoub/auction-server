<?php

namespace App\Services;

use App\Exceptions\BidSubmissionValidationException;
use App\Models\Bid;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BidService
{

    public function submit($itemId, $userId, $amount)
    {
        return DB::transaction(function () use ($itemId, $userId, $amount) {

            $lastBid = Bid::where('item_id', $itemId)
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $item = Item::lockForUpdate()->findOrFail($itemId);

            if ($lastBid != null) {
                if ($lastBid->amount >= $amount)
                    throw new BidSubmissionValidationException('There is a higher bid then yours');

                if ($lastBid->user_id ==  auth()->user()->id)
                    throw new BidSubmissionValidationException('You are the highest bidder already');
            }

            if (now()->greaterThan(Carbon::parse($item->auction_closes_at)))
                throw new BidSubmissionValidationException('The auction is already closed');

            if ($amount < $item->startingPrice)
                throw new BidSubmissionValidationException('Insufficient Amount');

            $bid = Bid::create([
                'user_id' =>  $userId,
                'item_id' => $itemId,
                'amount' => $amount,
            ]);

            return $bid;
        });
    }
}
