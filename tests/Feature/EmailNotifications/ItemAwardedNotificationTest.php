<?php

namespace Tests\Feature\EmailNotifications;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Events\AuctionClosedEvent;
use App\Events\BidSubmittedEvent;
use App\Jobs\AuctionClosedEventDispatcher;
use App\Listeners\SendItemAwardedNotifications;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use App\Notifications\ItemAwardedNotification;
use App\Notifications\NewSubmittedBid;
use App\Services\BidService;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ItemAwardedNotificationTest extends TestCase
{

    protected $item;
    protected $users;
    protected $adminUser;

    protected BidService $bidService;

    final const ITEM_REQ_URI = '/api/items';


    public function setUp(): void
    {
        parent::setUp();

        $this->bidService = new BidService();

        $this->users = User::factory(3)->create(['isAdmin' => false]);
        $this->adminUser = User::factory(1)->create(['isAdmin' => true])->first();
    }


    public function test_auction_closed_event_dispatcher_job_is_delayed_after_creating_an_item()
    {
        Queue::fake();

        $this->actingAs($this->adminUser);

        $imageFile = UploadedFile::fake()->image('avatar.jpg');

        $data = Item::factory(1)->raw([
            'image' => $imageFile,
        ])[0];


        $res = $this->postJson(self::ITEM_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertCreated();

        Queue::assertPushed(AuctionClosedEventDispatcher::class, fn ($job) => !is_null($job->delay));
    }


    public function test_ItemAwardedNotification_dispatched_after_using_SendItemAwardedNotifications_event_listener()
    {

        Notification::fake();
        Mail::fake();

        $firstUser = $this->users[0];
        $secondUser = $this->users[1];

        $item = Item::factory(1)->create([
            'auction_closes_at' => now()->addDays(3),
            'startingPrice' => 90
        ])->first();

        Bid::create(
            [
                'item_id' =>  $item->id,
                'user_id' => $firstUser->id,
                'amount' =>  $item->price + 10,
            ]
        );

        Bid::create(
            [
                'item_id' => $item->id,
                'user_id' => $secondUser->id,
                'amount' =>  $item->price + 20,
            ]
        );

        (new SendItemAwardedNotifications())->handle(new AuctionClosedEvent($item));


        Notification::assertSentTo(
            [$firstUser],
            ItemAwardedNotification::class
        );
        Notification::assertSentTo(
            [$secondUser],
            ItemAwardedNotification::class
        );
    }
}
