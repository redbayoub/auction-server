<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class UpdateItemTest extends BaseItemTest
{
    public function setUp(): void
    {
        parent::setUp();

        Item::factory(3)->create();
    }

    private function getValidInput()
    {
        $imageFile = UploadedFile::fake()->image('avatar.jpg');

        $data = Item::factory(1)->raw([
            'image' => $imageFile,
        ])[0];
        return $data;
    }



    public function test_admin_can_update_item()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();
        $id = Item::first()->id;
        $res = $this->postJson(self::ITEM_REQ_URI . "/$id", $data);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['startingPrice'],
                'auction_closes_at' => $data['auction_closes_at']->toJSON(),
                'image' => env('APP_URL') . '/storage/images/' . $data['image']->hashName(),
            ]
        ]);
    }
    public function test_admin_cannot_update_item_without_name()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();
        unset($data['name']);
        $id = Item::first()->id;
        $res = $this->postJson(self::ITEM_REQ_URI . "/$id", $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }

    public function test_user_cannot_create_item()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();
        $id = Item::first()->id;
        $res = $this->postJson(self::ITEM_REQ_URI . "/$id", $data);

        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
