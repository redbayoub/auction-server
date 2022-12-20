<?php

namespace App\Listeners;

use App\Events\BidSubmittedEvent;
use App\Notifications\AutoBidBotUsageAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBotUsageAlert
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
        $user = $event->bid->user;

        if (
            !$event->bid->isBot ||
            $user->bot->isAlertSent ||
            $user->bot->maxAmount > $user->bot->minAmount
        )
            return true;
        $user->notify(new AutoBidBotUsageAlert($user->bot));
    }
}
