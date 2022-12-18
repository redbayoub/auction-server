<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;

class DeleteItemTest extends BaseItemTest
{
    public function setUp(): void
    {
        parent::setUp();

        Item::factory(3)->create();
    }

    public function test_admin_can_delete_item()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $id = Item::first()->id;
        $res = $this->deleteJson(self::ITEM_REQ_URI . "/$id");

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertDatabaseMissing("items", ['id' => 1]);

        $res->assertJson([
            'status' => 'success'
        ]);
    }

    public function test_user_cannot_delete_item()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $id = Item::first()->id;
        $res = $this->deleteJson(self::ITEM_REQ_URI . "/$id");


        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
