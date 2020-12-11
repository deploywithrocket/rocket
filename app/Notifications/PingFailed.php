<?php

namespace App\Notifications;

use App\Channels\DiscordChannel;
use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PingFailed extends Notification
{
    use Queueable;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function via($notifiable)
    {
        $via = [];

        $notifiable->discord_webhook_url ? $via[] = DiscordChannel::class : '';

        return $via;
    }

    public function toDiscord($notifiable)
    {
        return [
            'content' => null,
            'embed' => [
                'title' => 'The server returns an error ❌❌!',
                'fields' => [
                    ['name' => 'Project', 'value' => $notifiable->name],
                    ['name' => 'URL', 'value' => '[' . $notifiable->live_url . '](' . $notifiable->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$notifiable, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
            ],
            'type' => 'error',
        ];
    }

    public function toMail()
    {
        return [];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'The server returns an error ❌❌!',
        ];
    }
}
