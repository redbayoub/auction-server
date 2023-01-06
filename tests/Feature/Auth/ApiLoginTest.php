<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiLoginTest extends TestCase
{

    final const LOGIN_REQ_URI = '/api/auth/login';
    final const ME_REQ_URI = '/api/user';

    public function setUp(): void
    {
        parent::setUp();

        User::factory(1)->create([
            'username' => 'user1',
            'password' =>  Hash::make('user2'),
        ]);
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


        $res->assertJsonStructure([
            'status',
            'data' => [
                'token',
            ],
        ]);

        $res->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_user_can_get_his_info_using_generated_token()
    {
        $data = [
            'username' => 'user1',
            'password' => 'user2',
        ];

        $res = $this->postJson(self::LOGIN_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertOk();
        $token = $res->json()['data']['token'];

        $res2 = $this->getJson(self::ME_REQ_URI, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertNotNull($res2);

        $res2->assertOk();
    }

    public function test_user_cannot_login_using_wrong_credentials()
    {
        $data = [
            'username' => 'user1',
            'password' => 'blabla',
        ];

        $res = $this->postJson(self::LOGIN_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnauthorized();

        $res->assertJson([
            'status' => 'fail',
            'message' => 'Invalid login details',
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
}
