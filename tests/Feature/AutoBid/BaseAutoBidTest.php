<?php

namespace Tests\Feature\AutoBid;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Item;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

abstract class BaseAutoBidTest extends TestCase
{
    protected $item;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->item = Item::factory(1)->create([
            'auction_closes_at' => now()->addDays(3)
        ])->first();
    }

    public function getUrl($itemId)
    {
        return "/api/items/$itemId/auto-bid";
    }
}
