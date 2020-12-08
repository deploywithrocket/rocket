<?php

namespace App\Jobs;

use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EnvoyDeployJob implements ShouldQueue
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
        $this->defineTasks();

        $this->beforeHandle();

        try {
            $process = $this->ssh->execute([
                $this->scripts['assert:commit'],
                $this->scripts['setup:repository'],
                $this->scripts['setup:directories'],
                $this->scripts['deploy:starting'],
                $this->scripts['deploy:check'],
                $this->scripts['deploy:started'],
                $this->scripts['deploy:provisioning'],
                $this->scripts['deploy:fetch'],
                $this->scripts['deploy:release'],
                $this->scripts['deploy:link'],
                // $this->scripts['deploy:copy'],
                $this->scripts['deploy:dotenv'],
                $this->scripts['deploy:composer'],
                $this->scripts['deploy:npm'],
                $this->scripts['deploy:provisioned'],
                $this->scripts['deploy:building'],
                $this->scripts['deploy:build'],
                $this->scripts['deploy:built'],
                $this->scripts['deploy:publishing'],
                $this->scripts['deploy:symlink'],
                $this->scripts['deploy:publish'],
                $this->scripts['deploy:cronjobs'],
                $this->scripts['deploy:published'],
                $this->scripts['deploy:finishing'],
                $this->scripts['deploy:cleanup'],
                $this->scripts['deploy:finished'],
            ]);

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch (\Throwable $th) {
            $this->afterHandleFailed();

            throw $th;
        }

        $this->afterHandle();
    }
}
