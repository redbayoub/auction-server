<?php

namespace App\Listeners;

use App\Events\AuctionClosedEvent;
use App\Models\Bid;
use App\Models\User;
use App\Notifications\ItemAwardedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendItemAwardedNotifications
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
    public function handle(AuctionClosedEvent $event)
    {
        if ($event->item->bids()->count() == 0)
            return false;

        Bid::where('item_id', $event->item->id)
            ->distinct('user_id')
            ->select(['user_id', 'id'])
            ->chunk(50, fn ($chunk) => $chunk->each(fn ($res) => User::find($res->user_id)->notify(new ItemAwardedNotification($event->item))));
    }
}
