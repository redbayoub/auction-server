<?php

namespace Tests\Feature\AutoBidBot;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\AutoBidItem;
use App\Models\Bot;
use App\Models\Item;
use App\Models\User;
use App\Services\BidService;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

class SubmitBidTest extends TestCase
{
    protected $item;
    protected $users;
    protected BidService $bidService;



    public function setUp(): void
    {
        parent::setUp();
        $this->bidService = new BidService();

        $this->users = User::factory(3)->create(['isAdmin' => false]);

        foreach ($this->users as $user) {
            Bot::create([
                'user_id' => $user->id,
                'maxAmount' => 150,
                'percentageAlert' => 90
            ]);
        }

        $this->item = Item::factory(1)->create([
            'auction_closes_at' => now()->addDays(3),
            'startingPrice' => 90
        ])->first();
    }

    public function test_user_can_win_a_bid_with_bot()
    {

        $firstUser = $this->users[0];
        $secondUser = $this->users[1];

        AutoBidItem::create([
            'user_id' => $firstUser->id,
            'item_id' => $this->item->id,
        ]);

        AutoBidItem::create([
            'user_id' => $secondUser->id,
            'item_id' => $this->item->id,
        ]);

        Bot::updateOrCreate(
            [
                'user_id' => $secondUser->id,
            ],
            [
                'maxAmount' => 200,
                'percentageAlert' => 90
            ]
        );

        $this->bidService->submit($this->item->id, $firstUser->id, $this->item->price + 10);

        $this->item->refresh();

        assertEquals($secondUser->id, $this->item->bid_user_id);

        assertGreaterThan(150, $this->item->price,);

        assertEquals(200 - $this->item->price, $secondUser->bot->refresh()->maxAmount);

        assertEquals(150, $firstUser->bot->refresh()->maxAmount);
    }
}
