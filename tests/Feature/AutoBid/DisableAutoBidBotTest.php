<?php

namespace Tests\Feature\AutoBid;

use App\Models\AutoBidItem;
use App\Models\User;

class DisableAutoBidBotTest extends BaseAutoBidTest
{

    public function test_user_can_disable_auto_bid_bot()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);
        
        AutoBidItem::create([
            'user_id' => $user->id,
            'item_id' => $this->item->id,
        ]);

        $res = $this->deleteJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertDatabaseCount('auto_bid_items', 0);

        $res->assertJson([
            'message' => 'Auto-Bid disabled successfully on the selected item',
            'status' => 'success',
        ]);
    }

    public function test_admin_cannot_disable_auto_bid_bot()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->deleteJson($this->getUrl($this->item->id));

        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
