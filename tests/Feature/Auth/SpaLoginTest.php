<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SpaLoginTest extends TestCase
{

    final const LOGIN_REQ_URI = '/login';
    final const LOGOUT_REQ_URI = '/logout';
    final const ME_REQ_URI = '/api/user';

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    public function test_user_can_login_using_username_and_password()
    {
        $data = [
            'username' => 'user1',
            'password' => 'user2',
        ];
        $res = $this->postJson(self::LOGIN_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_user_cannot_login_using_username_only()
    {
        $data = [
            'username' => 'user1',
        ];
        $res = $this->postJson(self::LOGIN_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }

    public function test_user_can_logout()
    {

        $user = User::first();
        $this->actingAs($user);

        $res = $this->postJson(self::LOGOUT_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_user_can_get_his_information()
    {
        $user = User::first();
        $this->actingAs($user);

        $res = $this->getJson(self::ME_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
            'data' => [
                'username' => $user->username
            ]
        ]);
    }
}
