<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;

class ListItemTest extends BaseItemTest
{
    public function setUp(): void
    {
        parent::setUp();

        Item::factory(20)->create();
    }
    public function test_user_can_list_items_with_pagination()
    {
        $user = User::first();
        $this->actingAs($user);

        $res = $this->getJson(self::ITEM_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonStructure([
            'status',
            'data' => [
                "data" => [
                    "*" => [
                        'name',
                        'description',
                        'price',
                        'auction_closes_at',
                        'image',
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

    public function test_user_can_list_items_and_search_and_sort()
    {
        $user = User::first();
        $this->actingAs($user);

        $item = Item::first();

        $search = $item->name;
        $res = $this->getJson(self::ITEM_REQ_URI . "?searchTerm=$search&sort=price:desc");

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonFragment([ "id" => $item->id]);
    }

    public function test_guest_cannot_list_items()
    {
        $res = $this->getJson(self::ITEM_REQ_URI);

        $this->assertNotNull($res);

        $res->assertUnauthorized();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }
}
