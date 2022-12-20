<?php

namespace Tests\Feature\Notifications;

use App\Notifications\TestNotification;

class MarkReadNotificationsTest extends BaseNotificationTest
{

    public function test_user_can_mark_read_notifications()
    {
        $this->actingAs($this->user);

        $this->user->notify(new TestNotification());

        $res = $this->putJson(self::NOTIFICATION_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertEquals(0, $this->user->refresh()->unreadNotifications()->count());
    }
}
