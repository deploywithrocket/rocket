<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Notifications\DeployFailed;
use App\Notifications\DeployStarted;
use App\Notifications\DeploySuccessful;
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
    protected Deployment $deployment;

    protected $tasks;
    protected $github_deployment_id;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function handle()
    {
        $this
            ->before()
            ->computeTasks()
            ->runTasks()
            ->after();
    }

    protected function before()
    {
        $this->deployment->status = 'in_progress';
        $this->deployment->started_at = now();
        $this->deployment->save();

        $this->ssh = SSH::create(
            $this->deployment->server->ssh_user,
            $this->deployment->server->ssh_host
        )
            ->usePrivateKey(Storage::path('keys/' . $this->deployment->server->id))
            ->disableStrictHostKeyChecking();

        $this->github_deployment_id = rescue(function () {
            $gh_client = $this->deployment->project->user->github()->deployments();
            [$user, $repo] = explode('/', $this->deployment->project->repository);

            return $gh_client->create($user, $repo, [
                'ref' => $this->deployment->commit['from_ref'] ? 'refs/' . $this->deployment->commit['from_ref'] : $this->deployment->commit['sha'],
                'environment' => $this->deployment->project->environment,
                'auto_merge' => false,
            ])['id'];
        }, null, false);

        $this->deployment->project->notify(new DeployStarted($this->deployment, $this->github_deployment_id));

        return $this;
    }

    protected function computeTasks()
    {
        $sh_renderer = new ShellScriptRenderer($this->deployment);

        $this->defineTask('start', [
            $sh_renderer->render('setup.repository'),
            $sh_renderer->render('setup.directories'),
            $sh_renderer->render('deploy.check'),
            $sh_renderer->render('hooks.started'),
        ]);

        $this->defineTask('provision', [
            $sh_renderer->render('deploy.fetch'),
            $sh_renderer->render('deploy.clone'),
            $sh_renderer->render('deploy.link'),
            $sh_renderer->render('deploy.dotenv'),
            $sh_renderer->render('deploy.composer'),
            $sh_renderer->render('deploy.npm'),
            $sh_renderer->render('hooks.provisioned'),
        ]);

        $this->defineTask('build', [
            $sh_renderer->render('deploy.build'),
            $sh_renderer->render('hooks.built'),
        ]);

        $this->defineTask('publish', [
            $sh_renderer->render('deploy.symlink'),
            $sh_renderer->render('deploy.cronjobs'),
            $sh_renderer->render('hooks.published'),
        ]);

        $this->defineTask('finish', [
            $sh_renderer->render('deploy.cleanup'),
            $sh_renderer->render('hooks.finished'),
        ]);

        return $this;
    }

    protected function defineTask($name, $commands)
    {
        $this->tasks[$name] = $commands;

        return $this;
    }

    protected function runTasks()
    {
        foreach ($this->tasks as $name => $task) {
            $this->runTask($name);
        }

        return $this;
    }

    protected function runTask($name)
    {
        $this->ssh->onOutput(fn ($type, $line) => $this->appendToOutput($type, $line, $name));

        $process = $this->ssh->execute($this->tasks[$name]);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    protected function after()
    {
        $this->deployment->status = 'success';
        $this->deployment->ended_at = now();
        $this->deployment->save();

        $this->deployment->project->notify(new DeploySuccessful($this->deployment, $this->github_deployment_id));

        dispatch(new PingJob($this->deployment));

        return $this;
    }

    public function failed()
    {
        $this->deployment->status = 'error';
        $this->deployment->ended_at = now();
        $this->deployment->save();

        $this->deployment->project->notify(new DeployFailed($this->deployment, $this->github_deployment_id));
    }

    protected function appendToOutput($buffer)
    {
        $this->deployment->raw_output .= $buffer;
        $this->deployment->save();
    }
}
