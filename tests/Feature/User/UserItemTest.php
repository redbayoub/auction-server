<?php

namespace Tests\Feature\User;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Enums\BidStatus;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserItemTest extends TestCase
{

    final const USER_ITEM_REQ_URI = '/api/user/items';


    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    public function test_user_can_list_his_awarded_items()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $items = Item::factory(2)->create();
        $firstItem = $items[0];
        $secondItem = $items[1];

        Bid::create([
            'user_id' => $user->id,
            'item_id' =>  $firstItem->id,
            'amount' =>  $firstItem->price + 10,
        ]);

        Bid::create([
            'user_id' => $user->id,
            'item_id' => $secondItem->id,
            'amount' => $secondItem->price + 10,
        ]);

        $firstItem->update([
            'auction_closes_at' => now()->subMinute(),
        ]);

        $res = $this->getJson(self::USER_ITEM_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonStructure([
            'status',
            'data' => [
                "*" => [
                    'id',
                    'name',
                    'price',
                    'bill_url'
                ],
            ]
        ]);

        $res->assertJsonFragment(['id' => $firstItem->id]);
        $res->assertJsonMissing(['id' => $secondItem->id]);
    }

    public function test_admin_cannot_list_his_items()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->getJson(self::USER_ITEM_REQ_URI);

        $this->assertNotNull($res);

        $res->assertForbidden();
    }
}
