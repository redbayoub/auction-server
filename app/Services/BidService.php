<?php

namespace App\Services;

use App\Events\BidSubmittedEvent;
use App\Exceptions\BidSubmissionValidationException;
use App\Models\Bid;
use App\Models\Bot;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BidService
{

    public function submit($itemId, $userId, $amount, $isBot = false)
    {
        return DB::transaction(function () use ($itemId, $userId, $amount, $isBot) {

            $lastBid = Bid::where('item_id', $itemId)
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $item = Item::lockForUpdate()->findOrFail($itemId);


            if ($isBot) {
                $bot = Bot::lockForUpdate()->where('user_id', $userId)->first();

                if ($bot->maxAmount < $amount)
                    throw new BidSubmissionValidationException('Insufficient Amount');

                $bot->decrement('maxAmount', $amount);
            }


            if ($lastBid != null) {
                if ($lastBid->isBot) {
                    $oldBot = Bot::lockForUpdate()->where('user_id', $lastBid->user_id)->first();
                    $oldBot->increment('maxAmount', $lastBid->amount);
                }

                if ($lastBid->amount >= $amount)
                    throw new BidSubmissionValidationException('There is a higher bid then yours');

                if ($lastBid->user_id ==  $userId)
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
                'isBot' => $isBot
            ]);

            BidSubmittedEvent::dispatch($bid);

            return $bid;
        });
    }
}
