<?php

namespace App\Notifications;

use App\Channels\DiscordChannel;
use App\Channels\GithubStatusUpdateChannel;
use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeploySuccessful extends Notification
{
    use Queueable;

    protected Deployment $deployment;
    protected $github_deployment_id;

    public function __construct(Deployment $deployment, $github_deployment_id = null)
    {
        $this->deployment = $deployment;
        $this->github_deployment_id = $github_deployment_id;
    }

    public function via($notifiable)
    {
        $via = [];

        $notifiable->discord_webhook_url ? $via[] = DiscordChannel::class : '';
        $this->github_deployment_id ? $via[] = GithubStatusUpdateChannel::class : '';

        return $via;
    }

    public function toDiscord($notifiable)
    {
        return [
            'content' => null,
            'embed' => [
                'title' => 'Application is now live 🚀🚀!',
                'description' => 'See by yourself: [' . $notifiable->live_url . '](' . $notifiable->live_url . ')',
                'fields' => [
                    ['name' => 'Project', 'value' => $notifiable->name],
                    ['name' => 'URL', 'value' => '[' . $notifiable->live_url . '](' . $notifiable->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$notifiable, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => substr($this->deployment->commit['sha'], 0, 7) . ($this->deployment->commit['from_ref'] ? ' (' . $this->deployment->commit['from_ref'] . ')' : ''), 'inline' => true],
                    ['name' => 'Commit message', 'value' => $this->deployment->commit['message'], 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
            ],
            'type' => 'success',
        ];
    }

    public function toGithubStatusUpdate($notifiable)
    {
        [$user, $repo] = explode('/', $notifiable->repository);

        return [
            'user' => $user,
            'repo' => $repo,
            'id' => $this->github_deployment_id,
            'payload' => [
                'state' => 'success',
                'log_url' => route('projects.deployments.show', [$notifiable, $this->deployment]),
                'environment_url' => $notifiable->live_url,
            ],
        ];
    }

    public function toMail()
    {
        return [];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Application is now live 🚀🚀!',
        ];
    }
}
