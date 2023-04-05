<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Models\DeploymentTask;
use App\Utils\ShellScriptRenderer;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Throwable;

class DeployJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ssh;
    protected $tasks;
    protected $github_deployment_id;

    public function __construct(
        protected Deployment $deployment
    ) {
    }

    public function handle():void
    {
        $this
            ->before()
            ->computeTasks()
            ->runTasks();
    }

    protected function before()
    {
        $this->deployment->status = 'in_progress';
        $this->deployment->started_at = now();
        $this->deployment->save();

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
        $jobs = [];

        foreach ($this->tasks as $name => $task) {
            $model = DeploymentTask::create([
                'deployment_id' => $this->deployment->id,
                'server_id' => $this->deployment->server_id,
                'name' => $name,
                'commands' => $task,
            ]);

            $jobs[] = (new ProcessDeploymentTaskJob($model));
        }

        $deployment = $this->deployment;

        $batch = Bus::batch([
           [...$jobs],
        ])->then(function (Batch $batch) use ($deployment) {
            $deployment->status = 'success';
            $deployment->save();
        })->catch(function (Batch $batch, Throwable $e) use ($deployment) {
            $deployment->status = 'error';
            $deployment->save();
        })->finally(function (Batch $batch) use ($deployment) {
            $deployment->ended_at = now();
            $deployment->save();
        })->dispatch();

        return $this;
    }
}
