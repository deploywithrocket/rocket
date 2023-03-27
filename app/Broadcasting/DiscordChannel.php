<?php

namespace App\Broadcasting;

use App\Utils\Discord;
use Illuminate\Notifications\Notification;

class DiscordChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toDiscord($notifiable);

        rescue(fn () => (new Discord($notifiable->discord_webhook_url))->webhook(
            $message['content'],
            $message['embed'],
            $message['type']
        ));
    }
}
