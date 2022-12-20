<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AutoBidBotUsageAlert extends Notification
{
    use Queueable;

    private $bot;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($bot)
    {
        $this->bot = $bot;
        $this->bot->update(['isAlertSent' => true]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Auto-Bid Bot Usage Alert',
            'body' => 'The auto-bid bot has used ' . $this->bot->percentageAlert . '% of the max amount',
        ];
    }
}
