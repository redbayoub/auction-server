<?php

namespace App\Listeners;

use App\Events\BidSubmittedEvent;
use App\Models\Bid;
use App\Models\User;
use App\Notifications\NewSubmittedBid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewSubmittedBidNotifications
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
        Bid::where('item_id', $event->bid->item_id)
            ->where('user_id', '!=', $event->bid->user_id)
            ->distinct('user_id')
            ->select(['user_id', 'id'])
            ->chunk(50, fn ($chunk) => $chunk->each(fn ($res) => User::find($res->user_id)->notify(new NewSubmittedBid($event->bid))));
    }
}
