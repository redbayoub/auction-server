<?php

namespace Tests\Feature\User;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Enums\BidStatus;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserBidTest extends TestCase
{

    final const USER_BID_REQ_URI = '/api/user/bids';


    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    public function test_user_can_list_his_bids_with_status()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $item = Item::factory(1)->create()->first();

        Bid::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'amount' => $item->price + 10,
        ]);

        Bid::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'amount' => $item->price + 20,
        ]);

        $res = $this->getJson(self::USER_BID_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonStructure([
            'status',
            'data' => [
                "*" => [
                    'user_id',
                    'item_id',
                    'item' => [
                        'name',
                    ],
                    'amount',
                    'status',
                ],
            ]
        ]);

        $res->assertJsonFragment(['item_id' => $item->id, 'amount' => $item->price + 20, 'status' => BidStatus::IN_PROGRESS->value]);
    }

    public function test_admin_cannot_list_his_bids()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->getJson(self::USER_BID_REQ_URI);

        $this->assertNotNull($res);

        $res->assertForbidden();
    }
}
