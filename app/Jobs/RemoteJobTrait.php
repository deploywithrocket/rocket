<?php

namespace App\Jobs;

use App\Http\Utils\Discord;
use Illuminate\Support\Facades\Storage;
use Spatie\Ssh\Ssh as SSH;

trait RemoteJobTrait
{
    protected $ssh;
    protected $scripts;
    protected $xsp;

    public function defineConfig()
    {
        $idk_how_to_use_a_closure = fn ($type, $line) => $this->appendToOutput($line);

        $this->ssh = SSH::create(
            $this->deployment->server->ssh_user,
            $this->deployment->server->ssh_host
        )
            ->usePrivateKey(Storage::path('keys/' . $this->deployment->server->id))
            ->onOutput($idk_how_to_use_a_closure)
            ->disableStrictHostKeyChecking();
    }

    public function beforeHandle()
    {
        $this->deployment->status = 'in_progress';
        $this->deployment->started_at = now();
        $this->deployment->save();

        rescue(function () {
            $gh_client = $this->deployment->project->user->github()->deployments();
            [$user, $repo] = explode('/', $this->deployment->project->repository);

            $this->xsp = $gh_client->create($user, $repo, [
                'ref' => $this->deployment->commit['from_ref'] ? 'refs/' . $this->deployment->commit['from_ref'] : $this->deployment->commit['sha'],
                'environment' => $this->deployment->project->environment,
                'auto_merge' => false,
            ])['id'];

            $gh_client->updateStatus($user, $repo, $this->xsp, [
                'state' => 'pending',
                'log_url' => route('projects.deployments.show', [$this->deployment->project, $this->deployment]),
                'environment_url' => $this->deployment->project->live_url,
            ]);
        });

        if ($this->deployment->project->discord_webhook_url) {
            rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                'title' => 'Deployment started 🔥🔥!',
                'fields' => [
                    ['name' => 'Project', 'value' => $this->deployment->project->name],
                    ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => substr($this->deployment->commit['sha'], 0, 7) . ($this->deployment->commit['from_ref'] ? ' (' . $this->deployment->commit['from_ref'] . ')' : ''), 'inline' => true],
                    ['name' => 'Commit message', 'value' => $this->deployment->commit['message'], 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
            ], 'info'));
        }
    }

    public function afterHandle()
    {
        $this->deployment->status = 'success';
        $this->deployment->ended_at = now();
        $this->deployment->save();

        if ($this->xsp ?? null) {
            rescue(function () {
                $gh_client = $this->deployment->project->user->github()->deployments();
                [$user, $repo] = explode('/', $this->deployment->project->repository);

                $gh_client->updateStatus($user, $repo, $this->xsp, [
                    'state' => 'success',
                    'log_url' => route('projects.deployments.show', [$this->deployment->project, $this->deployment]),
                    'environment_url' => $this->deployment->project->live_url,
                ]);
            });
        }

        if ($this->deployment->project->discord_webhook_url) {
            rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                'title' => 'Application is now live 🚀🚀!',
                'description' => 'See by yourself: [' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')',
                'fields' => [
                    ['name' => 'Project', 'value' => $this->deployment->project->name],
                    ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => substr($this->deployment->commit['sha'], 0, 7) . ($this->deployment->commit['from_ref'] ? ' (' . $this->deployment->commit['from_ref'] . ')' : ''), 'inline' => true],
                    ['name' => 'Commit message', 'value' => $this->deployment->commit['message'], 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
            ], 'success'));
        }
    }

    public function afterHandleFailed()
    {
        $this->deployment->status = 'error';
        $this->deployment->ended_at = now();
        $this->deployment->save();

        if ($this->xsp ?? null) {
            rescue(function () {
                $gh_client = $this->deployment->project->user->github()->deployments();
                [$user, $repo] = explode('/', $this->deployment->project->repository);

                $gh_client->updateStatus($user, $repo, $this->xsp, [
                    'state' => 'error',
                    'log_url' => route('projects.deployments.show', [$this->deployment->project, $this->deployment]),
                    'environment_url' => $this->deployment->project->live_url,
                ]);
            });
        }

        if ($this->deployment->project->discord_webhook_url) {
            rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                'title' => 'Your project has failed to deploy 😭😭',
                'description' => '[See the deployment log](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')',
                'fields' => [
                    ['name' => 'Project', 'value' => $this->deployment->project->name],
                    ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => substr($this->deployment->commit['sha'], 0, 7) . ($this->deployment->commit['from_ref'] ? ' (' . $this->deployment->commit['from_ref'] . ')' : ''), 'inline' => true],
                    ['name' => 'Commit message', 'value' => $this->deployment->commit['message'], 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
        ], 'error'));
        }
    }

    public function appendToOutput($buffer)
    {
        $this->deployment->raw_output .= $buffer;
        $this->deployment->save();
    }
}
