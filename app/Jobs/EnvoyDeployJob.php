<?php

namespace App\Jobs;

use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
        $this->beforeHandle();
        $this->defineConfig();
        $this->defineTasks();

        try {
            $process = $this->ssh->execute([
                $this->scripts['preflight'],
                $this->scripts['assert:commit'],
                $this->scripts['deploy:starting'],
                $this->scripts['deploy:check'],
                $this->scripts['deploy:backup'],
                $this->scripts['deploy:started'],
                $this->scripts['deploy:provisioning'],
                $this->scripts['deploy:fetch'],
                $this->scripts['deploy:release'],
                $this->scripts['deploy:link'],
                // $this->scripts['deploy:copy'],
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
            $this->deployment->status = 'error';
            $this->deployment->save();

            dump($this->deployment->raw_output);
            throw $th;
        }

        $this->deployment->refresh();
        dd($this->deployment->raw_output);

        $this->afterHandle();
    }
}
