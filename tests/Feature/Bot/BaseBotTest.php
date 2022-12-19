<?php

namespace Tests\Feature\Bot;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Database\Seeders\UserSeeder;
use Tests\TestCase;

abstract class BaseBotTest extends TestCase
{
    final const BOT_REQ_URI = '/api/user/bot';

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
}
