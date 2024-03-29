<?php

namespace Tests\Feature\Item;

use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class GetItemTest extends BaseItemTest
{
    public function setUp(): void
    {
        parent::setUp();

        Item::factory(3)->create();
    }


    public function test_user_can_get_item()
    {
        $user = User::first();
        $this->actingAs($user);

        $item = Item::first();
        $id = $item->id;
        $res = $this->getJson(self::ITEM_REQ_URI . "/$id");

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->startingPrice,
                'auction_closes_at' => $item->auction_closes_at->toJSON(),
            ]
        ]);
    }

    public function test_user_can_get_item_with_awarded_user_name_when_auction_closes()
    {
        $user = User::first();
        $this->actingAs($user);

        $item =  Item::factory(1)->create([
            'auction_closes_at' => now()->subMinutes(10),
        ])->first();

        $bid = Bid::factory(1)->create([
            'item_id' => $item->id,
            'amount' => $item->startingPrice + 10,
        ])->first();

        $id = $item->id;
        $res = $this->getJson(self::ITEM_REQ_URI . "/$id");

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonFragment([
            'bid_username' => $bid->user->username,
        ]);
    }

    public function test_user_cannot_get_awarded_user_name_when_auction_is_not_closed()
    {
        $user = User::first();
        $this->actingAs($user);

        $item =  Item::factory(1)->create([
            'auction_closes_at' => now()->addMinutes(10),
        ])->first();

        $bid = Bid::factory(1)->create([
            'item_id' => $item->id,
            'amount' => $item->startingPrice + 10,
        ])->first();

        $id = $item->id;
        $res = $this->getJson(self::ITEM_REQ_URI . "/$id");

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonMissing([
            'bid_username' => $bid->user->username,
        ]);
    }


    public function test_guest_cannot_get_item()
    {
        $id = Item::first()->id;
        $res = $this->getJson(self::ITEM_REQ_URI . "/$id");

        $this->assertNotNull($res);
        $res->assertUnauthorized();
    }
}
