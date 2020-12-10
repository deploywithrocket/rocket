<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Utils\ShellScriptRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DeployJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RemoteJobTrait;

    public $tries = 1;
    public $timeout = 60 * 15;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function handle()
    {
        $this->defineConfig();
        $this->beforeHandle();

        $sh_renderer = new ShellScriptRenderer($this->deployment);

        try {
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
        } catch (\Throwable $th) {
            $this->afterHandleFailed();

            throw $th;
        }

        $this->afterHandle();

        dispatch(new PingJob($this->deployment));
    }
}
