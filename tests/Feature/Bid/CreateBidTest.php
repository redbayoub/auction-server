<?php

namespace Tests\Feature\Bid;

use App\Models\Bid;
use App\Models\Item;
use App\Models\User;

class CreateBidTest extends BaseBidTest
{
    private $item;

    public function setUp(): void
    {
        parent::setUp();

        $this->item = Item::factory(1)->create([
            'startingPrice' => 30,
            'auction_closes_at' => now()->addMinutes(60)
        ])->first();
    }

    public function test_user_can_submit_a_bid()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);


        $data = [
            'amount' => $this->item->startingPrice + 10,
        ];
        $res = $this->postJson($this->getUrl($this->item->id), $data);

        $this->assertNotNull($res);

        $res->assertCreated();

        $this->assertDatabaseCount('bids', 1);

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'item_id' => $this->item->id,
                'user_id' => $user->id,
                'amount' => $data['amount'],
            ]
        ]);
    }

    public function test_user_cannot_submit_a_bid_without_amount()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);


        $data = [];
        $res = $this->postJson($this->getUrl($this->item->id), $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }

    public function test_user_cannot_submit_bid_with_amount_less_then_starting_price()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);


        $data = [
            'amount' => $this->item->startingPrice - 10,
        ];
        $res = $this->postJson($this->getUrl($this->item->id), $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
            'data' => [
                'amount' => [
                    'Insufficient Amount'
                ]
            ]
        ]);
    }

    public function test_user_cannot_submit_bid_when_auction_closed()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $item = Item::factory(1)->create([
            'startingPrice' => 30,
            'auction_closes_at' => now()->subDay()
        ])->first();


        $data = [
            'amount' =>  $item->startingPrice + 10,
        ];
        $res = $this->postJson($this->getUrl($item->id), $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
            'data' => [
                'amount' => [
                    'The auction is already closed'
                ]
            ]
        ]);
    }

    public function test_user_cannot_submit_a_bid_when_a_higher_bid_is_available()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        Bid::create([
            'user_id' => User::factory(1)->create()->first()->id,
            'item_id' => $this->item->id,
            'amount' => $this->item->startingPrice + 10,
        ]);


        $data = [
            'amount' => $this->item->startingPrice + 5,
        ];
        $res = $this->postJson($this->getUrl($this->item->id), $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
            'data' => [
                'amount' => [
                    'There is a higher bid then yours'
                ]
            ]
        ]);
    }

    public function test_user_cannot_submit_a_bid_when_he_is_the_highest_bidder()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        Bid::create([
            'user_id' => $user->id,
            'item_id' => $this->item->id,
            'amount' => $this->item->startingPrice + 100,
        ]);


        $data = [
            'amount' => $this->item->startingPrice + 200,
        ];
        $res = $this->postJson($this->getUrl($this->item->id), $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
            'data' => [
                'amount' => [
                    'You are the highest bidder already'
                ]
            ]
        ]);
    }


    public function test_admin_cannot_submit_a_bid()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $data = [
            'amount' => $this->item->startingPrice + 10,
        ];
        $res = $this->postJson($this->getUrl($this->item->id), $data);


        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
