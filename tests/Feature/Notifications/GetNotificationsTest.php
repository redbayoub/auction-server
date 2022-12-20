<?php

namespace Tests\Feature\Notifications;

use App\Notifications\TestNotification;

class GetNotificationsTest extends BaseNotificationTest
{

    public function test_user_can_get_notifications()
    {
        $this->actingAs($this->user);

        $this->user->notify(new TestNotification());

        $res = $this->getJson(self::NOTIFICATION_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $res->assertJsonFragment([
            'title' => 'Test Notification'
        ]);
    }

    public function test_guest_cannot_get_notifications()
    {
        $res = $this->getJson(self::NOTIFICATION_REQ_URI);

        $this->assertNotNull($res);

        $res->assertUnauthorized();
    }
}
