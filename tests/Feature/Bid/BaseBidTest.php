<?php

namespace Tests\Feature\Bid;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Database\Seeders\UserSeeder;
use Tests\TestCase;

abstract class BaseBidTest extends TestCase
{
    
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    public function getUrl($itemId)
    {
        return "/api/items/$itemId/bids";
    }
}
