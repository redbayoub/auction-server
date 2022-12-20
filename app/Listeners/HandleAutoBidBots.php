<?php

namespace App\Listeners;

use App\Events\BidSubmittedEvent;
use App\Models\Bot;
use App\Services\BidService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class HandleAutoBidBots
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BidSubmittedEvent $event)
    {
        $item_id = $event->bid->item_id;
        $currentBidAmount = $event->bid->amount;
        $bidService = new BidService();

        $queryResults = DB::table('bots')
            ->join('auto_bid_items', 'bots.user_id', '=', 'auto_bid_items.user_id')
            ->where('auto_bid_items.item_id', $item_id)
            ->where('bots.maxAmount', '>',  $currentBidAmount)
            ->orderByDesc('bots.maxAmount')
            ->limit(2)
            ->get('bots.*');

        $competingBots = Bot::hydrate($queryResults->all());

        if (count($competingBots) == 0) return false;

        $winnerBot = null;
        $nextBidAmount = null;
        if (count($competingBots) == 2) {
            $winnerBot = $competingBots[0];
            $secondBot = $competingBots[1];
            $nextBidAmount = $secondBot->maxAmount + 1;
        } else {
            $winnerBot = $competingBots[0];
            $nextBidAmount = $currentBidAmount + 1;
        }

        $bidService->submit($item_id, $winnerBot->user_id,  $nextBidAmount, true);

        return true;
    }
}
