<?php

namespace Tests\Feature\AutoBid;

use App\Models\AutoBidItem;
use App\Models\User;

class GetAutoBidBotTest extends BaseAutoBidTest
{
    private $autoBidItem, $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('isAdmin', false)->first();

        $this->autoBidItem = AutoBidItem::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);
    }

    public function test_user_can_get_auto_bid_item()
    {
        $this->actingAs($this->user);

        $res = $this->getJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $this->autoBidItem->id
            ]
        ]);
    }

    public function test_admin_cannot_get_auto_bid_item()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->getJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
