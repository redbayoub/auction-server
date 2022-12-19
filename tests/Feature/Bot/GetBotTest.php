<?php

namespace Tests\Feature\Bot;

use App\Models\Bot;
use App\Models\User;

class GetBotTest extends BaseBotTest
{
    private $user, $bot;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('isAdmin', false)->first();

        $this->bot = Bot::create([
            'user_id' => $this->user->id,
            'maxAmount' => 100,
            'percentageAlert' => 90
        ]);
    }


    public function test_user_can_get_bot_configs()
    {
        $this->actingAs($this->user);

        $res = $this->getJson(self::BOT_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonFragment([
            'user_id' => $this->user->id,
            'maxAmount' => $this->bot->maxAmount,
            'percentageAlert' => $this->bot->percentageAlert,
        ]);
    }

    public function test_admin_cannot_get_bot_configs()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $res = $this->getJson(self::BOT_REQ_URI);

        $this->assertNotNull($res);
        
        $res->assertForbidden();
    }
}
