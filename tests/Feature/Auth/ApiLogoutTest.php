<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiLogoutTest extends TestCase
{

    final const LOGOUT_REQ_URI = '/api/auth/logout';

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory(1)->create([
            'isAdmin' => false,
        ])->first();
    }

    public function test_user_can_logout()
    {
        $this->actingAs($this->user);

        $res = $this->deleteJson(self::LOGOUT_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $res = $this->deleteJson(self::LOGOUT_REQ_URI);

        $this->assertNotNull($res);

        $res->assertUnauthorized();

        $res->assertJson([
            'status' => 'fail',
        ]);
    }
}
