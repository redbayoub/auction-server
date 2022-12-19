<?php

namespace Tests\Feature\Bot;

use App\Models\Bot;
use App\Models\User;

class CreateBotTest extends BaseBotTest
{

    private function getValidInput()
    {
        $data = [
            'maxAmount' => 100,
            'percentageAlert' => 90
        ];
        return $data;
    }

    public function test_user_can_create_bot()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();
        $res = $this->putJson(self::BOT_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertDatabaseCount('bots', 1);

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'maxAmount' => $data['maxAmount'],
                'percentageAlert' => $data['percentageAlert'],
            ]
        ]);
    }

    public function test_user_can_update_bot()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();

        Bot::create([
            'user_id' => $user->id,
            'maxAmount' => $data['maxAmount'] - 20,
            'percentageAlert' => $data['percentageAlert'] - 10,
        ]);

        $res = $this->putJson(self::BOT_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertDatabaseCount('bots', 1);

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'maxAmount' => $data['maxAmount'],
                'percentageAlert' => $data['percentageAlert'],
            ]
        ]);
    }

    public function test_user_cannot_create_item_without_maxAmount()
    {
        $user = User::where('isAdmin', false)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();
        unset($data['maxAmount']);
        $res = $this->putJson(self::BOT_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }

    public function test_admin_cannot_create_bot()
    {
        $user = User::where('isAdmin', true)->first();
        $this->actingAs($user);

        $data = $this->getValidInput();
        $res = $this->putJson(self::BOT_REQ_URI, $data);

        $this->assertNotNull($res);
        $res->assertForbidden();
    }
}
