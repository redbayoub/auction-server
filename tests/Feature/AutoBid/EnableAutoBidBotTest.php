<?php

namespace Tests\Feature\AutoBid;

use App\Models\Bot;
use App\Models\User;

class EnableAutoBidBotTest extends BaseAutoBidTest
{

    public function test_user_can_enable_auto_bid_bot()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        Bot::create([
            'user_id' => $user->id,
            'maxAmount' => 100,
            'percentageAlert' => 90
        ]);

        $res = $this->postJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertDatabaseCount('auto_bid_items', 1);

        $res->assertJson([
            'message' => 'Auto-Bid enabled successfully on the selected item',
            'status' => 'success',
        ]);
    }

    public function test_user_cannot_enable_auto_bid_bot_if_bot_not_configured()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $res = $this->postJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);

        $res->assertStatus(400);

        $this->assertDatabaseCount('auto_bid_items', 0);

        $res->assertJson([
            'message' => 'You need to configure the bot setting before enabling auto-bid',
            'status' => 'fail',
        ]);
    }

    public function test_admin_cannot_enable_auto_bid_bot()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->deleteJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
