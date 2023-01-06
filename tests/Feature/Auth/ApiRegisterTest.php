<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiRegisterTest extends TestCase
{

    final const REGISTER_REQ_URI = '/api/auth/register';

    private User $admin;

    public function setUp(): void
    {
        parent::setUp();

        User::factory(1)->create([
            'email' => 'user@example.com',
            'username' => 'user1',
            'password' =>  'user2',
        ]);

        $this->admin =
            User::factory(1)->create([
                'isAdmin' => true,
            ])->first();
    }

    public function test_regular_user_can_register()
    {
        $data = [
            'username' => 'user3',
            'password' => 'user4',
            'email' => 'user3@example.com',
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertCreated();


        $res->assertJsonFragment([
            'status' => 'success',
            'username' => "user3",
        ]);
    }

    public function test_admin_user_can_register_another_admin()
    {
        $this->actingAs($this->admin);

        $data = [
            'username' => 'user3',
            'password' => 'user4',
            'email' => 'user3@example.com',
            'isAdmin' => true,
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertCreated();

        $res->assertJsonFragment([
            'status' => 'success',
            'username' => "user3",
        ]);
    }
    // -----------------
    public function test_regular_user_cannot_register_with_existing_email()
    {
        $data = [
            'username' => 'user3',
            'password' => 'user4',
            'email' => 'user@example.com',
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJsonStructure([
            'status',
            'data' => [
                'email',
            ],
        ]);
    }

    public function test_regular_user_cannot_register_with_existing_username()
    {
        $data = [
            'username' => 'user1',
            'password' => 'user4',
            'email' => 'user4@example.com',
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJsonStructure([
            'status',
            'data' => [
                'username',
            ],
        ]);
    }

    public function test_regular_user_cannot_register_with_missing_fields()
    {
        $data = [
            'username' => 'user3',
            'password' => 'user4',
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJsonStructure([
            'status',
            'data' => [
                'email',
            ],
        ]);
    }


    // -----------------


    public function test_regular_user_cannot_register_another_admin()
    {
        $data = [
            'username' => 'user3',
            'password' => 'user4',
            'email' => 'user3@example.com',
            'isAdmin' => true,
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertForbidden();

        $res->assertJsonFragment([
            'status' => 'fail',
        ]);
    }


    public function test_admin_user_cannot_register_another_admin_with_missing_fields()
    {
        $this->actingAs($this->admin);

        $data = [
            'username' => 'user3',
            'password' => 'user4',
            'isAdmin' => true,
        ];

        $res = $this->postJson(self::REGISTER_REQ_URI, $data);

        $this->assertNotNull($res);

        $res->assertUnprocessable();

        $res->assertJsonStructure([
            'status',
            'data' => [
                'email',
            ],
        ]);
    }
}
