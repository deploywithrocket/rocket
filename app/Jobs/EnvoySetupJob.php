<?php

namespace App\Jobs;

use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class EnvoySetupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60 * 15;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function handle()
    {
        $this->deployment->status = 'running';
        $this->deployment->save();

        $rocket_file = $this->deployment->id . '.rocket';

        Storage::put(
            $rocket_file,
            json_encode($this->deployment->extractVariables())
        );

        $deptrace = Storage::path($rocket_file);

        $process = new Process([
            base_path('vendor/bin/envoy'),
            'run',
            'setup',
            '--deptrace=' . $rocket_file,
        ]);

        $process->setWorkingDirectory(base_path());
        $process->setEnv(['TEMP' => storage_path('app')]);
        $process->setTimeout(60 * 15);

        $process->wait(function ($type, $buffer) {
            DB::update(
                'update deployments set raw_output = CONCAT_WS(IFNULL(raw_output, ""), ?), updated_at = ? where id = ?',
                [$buffer, now()->format('Y-m-d H:i:s'), $this->deployment->id]
            );
        });

        Storage::delete($rocket_file);

        if (! $process->isSuccessful()) {
            $this->deployment->status = 'error';
            $this->deployment->save();

            return;
        }

        $this->deployment->status = 'success';
        $this->deployment->save();
    }
}
