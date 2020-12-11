<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class GithubStatusUpdateChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toGithubStatusUpdate($notifiable);

        rescue(function () use ($notifiable, $message) {
            $gh_client = $notifiable->user->github()->deployments();

            $gh_client->updateStatus(
                $message['user'],
                $message['repo'],
                $message['id'],
                $message['payload']
            );
        });
    }
}
