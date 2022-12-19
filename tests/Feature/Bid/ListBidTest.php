<?php

namespace Tests\Feature\Bid;

use App\Models\Bid;
use App\Models\Item;
use App\Models\User;

class ListBidTest extends BaseBidTest
{

    private $item;
    final const ITEM_REQ_URI = '/api/items';


    public function setUp(): void
    {
        parent::setUp();

        $this->item = Item::factory(1)->has(Bid::factory(10))->create([
            'startingPrice' => 5,
            'auction_closes_at' => now()->addMinutes(60)
        ])->first();
    }
    public function test_admin_can_list_bids_with_pagination()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->getJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonStructure([
            'status',
            'data' => [
                "data" => [
                    "*" => [
                        'user_id',
                        'item_id',
                        'amount',
                        'user' => [
                            'username'
                        ]
                    ],
                ],
                "pagination" => [
                    'total',
                    'lastPage',
                    'perPage',
                    'currentPage',
                    'nextPageUrl',
                    'previousPageUrl',
                ]
            ]
        ]);
    }

    public function test_user_can_get_latest_price_of_item()
    {
        $user = User::first();
        $this->actingAs($user);

        $res = $this->getJson(self::ITEM_REQ_URI . "/" . $this->item->id);

        $this->assertNotNull($res);

        $res->assertOk();

        $highestBid = $this->item->bids->sortByDesc('amount')->first();

        $res->assertJsonFragment([
            "id" => $this->item->id,
            'bid_user_id' => $highestBid->user_id,
            'price' => $highestBid->amount,
        ]);
    }

    public function test_user_cannot_list_bids()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $res = $this->getJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);

        $res->assertForbidden();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }
}
