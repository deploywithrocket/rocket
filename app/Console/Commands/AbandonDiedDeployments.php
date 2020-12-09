<?php

namespace App\Console\Commands;

use App\Models\Deployment;
use Illuminate\Console\Command;

class AbandonDiedDeployments extends Command
{
    protected $signature = 'deployments:abandon-died';

    protected $timeout = 15;

    public function handle()
    {
        Deployment::query()
            ->where('status', 'in_progress')
            ->where('started_at', '<', now()->subMinutes($this->timeout))
            ->update(['status' => 'abandonned']);
    }
}
