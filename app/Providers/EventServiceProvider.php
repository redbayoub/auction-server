<?php

namespace App\Providers;

use App\Events\AuctionClosedEvent;
use App\Events\BidSubmittedEvent;
use App\Listeners\HandleAutoBidBots;
use App\Listeners\SendBotUsageAlert;
use App\Listeners\SendItemAwardedNotifications;
use App\Listeners\SendNewSubmittedBidNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BidSubmittedEvent::class => [
            HandleAutoBidBots::class,
            SendBotUsageAlert::class,
            SendNewSubmittedBidNotifications::class,
        ],
        AuctionClosedEvent::class => [
            SendItemAwardedNotifications::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
