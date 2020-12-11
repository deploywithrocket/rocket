<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Notifications\PingFailed;
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

        $ping = $this->deployment->ping()->create([
            'project_id' => $this->deployment->project_id,
            'status' => $response->failed() ? 'failed' : 'success',
            'status_code' => $response->status(),
            'request_duration' => $request_duration,
        ]);

        if ($response->failed()) {
            $ping->body = $response->body();
            $ping->save();

            $this->deployment->project->notify(new PingFailed($this->deployment));
        }
    }
}
