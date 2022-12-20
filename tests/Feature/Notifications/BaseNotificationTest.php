<?php

namespace Tests\Feature\Notifications;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Tests\TestCase;

abstract class BaseNotificationTest extends TestCase
{
    protected $user;

    final const NOTIFICATION_REQ_URI = '/api/user/notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory(1)->create(['isAdmin' => false])->first();
    }
}
