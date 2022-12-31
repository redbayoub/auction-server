<?php

namespace Tests\Feature\EmailNotifications;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Events\BidSubmittedEvent;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use App\Notifications\NewSubmittedBid;
use App\Services\BidService;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewSubmittedBidNotificationTest extends TestCase
{

    protected $item;
    protected $users;
    protected BidService $bidService;


    public function setUp(): void
    {
        parent::setUp();

        $this->bidService = new BidService();

        $this->users = User::factory(3)->create(['isAdmin' => false]);

        $this->item = Item::factory(1)->create([
            'auction_closes_at' => now()->addDays(3),
            'startingPrice' => 90
        ])->first();
    }


    public function test_user_can_get_new_bid_notification_after_a_bid_made_by_someone_else()
    {
        Notification::fake();
        Mail::fake();

        $firstUser = $this->users[0];
        $secondUser = $this->users[1];

        Bid::create(
            [
                'item_id' => $this->item->id,
                'user_id' => $firstUser->id,
                'amount' => $this->item->price + 10,
            ]
        );

        Notification::assertNothingSent();
        Mail::assertNothingSent();

        $this->bidService->submit($this->item->id, $secondUser->id, $this->item->price + 20);

        $this->assertDatabaseCount('bids',2);

        Notification::assertSentTo(
            [$firstUser],
            NewSubmittedBid::class
        );

        Notification::assertNotSentTo(
            [$secondUser],
            NewSubmittedBid::class
        );
    }
}
