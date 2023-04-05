<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Models\DeploymentTask;
use App\Models\Server;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\Ssh\Ssh as SSH;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessDeploymentTaskJob implements ShouldQueue
{
    use Dispatchable, Batchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?SSH $ssh;
    protected ?Deployment $deployment;
    protected ?Server $server;

    public function __construct(
        protected DeploymentTask $task,
    ) {
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $this
            ->before()
            ->runTask()
            ->after();
    }

    protected function before()
    {
        $this->deployment = $this->task->deployment;
        $this->server = $this->deployment->server;

        $this->ssh = SSH::create(
            $this->server->ssh_user,
            $this->server->ssh_host,
            $this->server->ssh_port,
        )
            ->usePrivateKey(Storage::path('keys/' . $this->server->id))
            ->disableStrictHostKeyChecking();

        $this->ssh->onOutput(fn ($type, $line) => $this->appendToOutput($type, $line));

        $this->task->status = 'in_progress';
        $this->task->started_at = now();
        $this->task->save();

        return $this;
    }

    protected function runTask()
    {
        if (! $this->task->commands) {
            return $this;
        }

        $process = $this->ssh->execute($this->task->commands);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    protected function after()
    {
        $this->task->status = 'success';
        $this->task->ended_at = now();
        $this->task->save();

        return $this;
    }

    public function failed()
    {
        $this->task->status = 'error';
        $this->task->ended_at = now();
        $this->task->save();
    }

    protected function appendToOutput($type, $line)
    {
        $this->task->output .= $line;

        $this->task->save();
    }
}
