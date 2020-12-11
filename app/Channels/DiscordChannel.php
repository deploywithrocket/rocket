<?php

namespace App\Channels;

use App\Http\Utils\Discord;
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
