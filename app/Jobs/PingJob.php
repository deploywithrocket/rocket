<?php

namespace App\Jobs;

use App\Models\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function handle()
    {
        $request_started_at = now();
        $response = Http::get($this->deployment->project->live_url);
        $request_duration = now()->diffInMilliseconds($request_started_at);

        if ($response->failed()) {
            $this->deployment->ping()->create([
                'project_id' => $this->deployment->project_id,
                'status' => 'failed',
                'body' => $response->body(),
                'status_code' => $response->status(),
                'request_duration' => $request_duration,
            ]);

            if ($this->deployment->project->discord_webhook_url) {
                rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                    'title' => 'The server returns an error ❌❌!',
                    'fields' => [
                        ['name' => 'Project', 'value' => $this->deployment->project->name],
                        ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                        ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                        ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                    ],
                ], 'error'));
            }

            return;
        }

        $this->deployment->ping()->create([
            'project_id' => $this->deployment->project_id,
            'status' => 'success',
            'status_code' => $response->status(),
            'request_duration' => $request_duration,
        ]);
    }
}
