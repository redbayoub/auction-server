<?php

namespace Tests\Feature\Notifications;

use App\Notifications\TestNotification;

class ClearNotificationsTest extends BaseNotificationTest
{

    public function test_user_can_clear_notifications()
    {
        $this->actingAs($this->user);

        $this->user->notify(new TestNotification());

        $res = $this->deleteJson(self::NOTIFICATION_REQ_URI);

        $this->assertNotNull($res);

        $res->assertOk();

        $this->assertEquals(0, $this->user->refresh()->notifications()->count());
    }
}
