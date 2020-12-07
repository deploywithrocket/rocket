<?php

namespace App\Jobs;

use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EnvoySetupJob implements ShouldQueue
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
                $this->scripts['setup:repository'],
                $this->scripts['setup:directories'],
            ]);

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch (\Throwable $th) {
            $this->deployment->status = 'error';
            $this->deployment->save();

            throw $th;
        }

        $this->afterHandle();
    }
}
