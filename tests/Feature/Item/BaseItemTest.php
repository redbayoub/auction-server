<?php

namespace Tests\Feature\Item;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Database\Seeders\UserSeeder;
use Tests\TestCase;

abstract class BaseItemTest extends TestCase
{
    final const ITEM_REQ_URI = '/api/items';

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
}
