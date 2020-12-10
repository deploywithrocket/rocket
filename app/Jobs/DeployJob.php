<?php

namespace App\Jobs;

use App\Http\Utils\Discord;
use App\Models\Deployment;
use App\Utils\ShellScriptRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\Ssh\Ssh as SSH;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DeployJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60 * 15;

    protected $ssh;
    protected $github_deployment_id;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function handle()
    {
        $this->ssh = SSH::create(
            $this->deployment->server->ssh_user,
            $this->deployment->server->ssh_host
        )
            ->usePrivateKey(Storage::path('keys/' . $this->deployment->server->id))
            ->onOutput(fn ($type, $line) => $this->appendToOutput($line))
            ->disableStrictHostKeyChecking();

        $this->before();
        $this->execute();
        $this->after();

        $sh_renderer = new ShellScriptRenderer($this->deployment);

        $process = $this->ssh->execute([
            $sh_renderer->render('setup.repository'),
            $sh_renderer->render('setup.directories'),
            $sh_renderer->render('deploy.check'),
            $sh_renderer->render('hooks.started'),
            $sh_renderer->render('deploy.fetch'),
            $sh_renderer->render('deploy.clone'),
            $sh_renderer->render('deploy.link'),
            $sh_renderer->render('deploy.dotenv'),
            $sh_renderer->render('deploy.composer'),
            $sh_renderer->render('deploy.npm'),
            $sh_renderer->render('hooks.provisioned'),
            $sh_renderer->render('deploy.build'),
            $sh_renderer->render('hooks.built'),
            $sh_renderer->render('deploy.symlink'),
            $sh_renderer->render('deploy.cronjobs'),
            $sh_renderer->render('hooks.published'),
            $sh_renderer->render('deploy.cleanup'),
            $sh_renderer->render('hooks.finished'),
        ]);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function before()
    {
        $this->deployment->status = 'in_progress';
        $this->deployment->started_at = now();
        $this->deployment->save();

        rescue(function () {
            $gh_client = $this->deployment->project->user->github()->deployments();
            [$user, $repo] = explode('/', $this->deployment->project->repository);

            $this->github_deployment_id = $gh_client->create($user, $repo, [
                'ref' => $this->deployment->commit['from_ref'] ? 'refs/' . $this->deployment->commit['from_ref'] : $this->deployment->commit['sha'],
                'environment' => $this->deployment->project->environment,
                'auto_merge' => false,
            ])['id'];

            $gh_client->updateStatus($user, $repo, $this->github_deployment_id, [
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

    protected function after()
    {
        $this->deployment->status = 'success';
        $this->deployment->ended_at = now();
        $this->deployment->save();

        if ($this->github_deployment_id ?? null) {
            rescue(function () {
                $gh_client = $this->deployment->project->user->github()->deployments();
                [$user, $repo] = explode('/', $this->deployment->project->repository);

                $gh_client->updateStatus($user, $repo, $this->github_deployment_id, [
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

        dispatch(new PingJob($this->deployment));
    }

    protected function failed()
    {
        $this->deployment->status = 'error';
        $this->deployment->ended_at = now();
        $this->deployment->save();

        if ($this->github_deployment_id ?? null) {
            rescue(function () {
                $gh_client = $this->deployment->project->user->github()->deployments();
                [$user, $repo] = explode('/', $this->deployment->project->repository);

                $gh_client->updateStatus($user, $repo, $this->github_deployment_id, [
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

    protected function appendToOutput($buffer)
    {
        $this->deployment->raw_output .= $buffer;
        $this->deployment->save();
    }
}
