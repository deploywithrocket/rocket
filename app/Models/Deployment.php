<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

    protected $casts = [
        'commit' => 'json',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected $appends = [
        'duration',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function ping()
    {
        return $this->hasOne(Ping::class);
    }

    public function getDurationAttribute()
    {
        return rescue(fn () => $this->ended_at->diff($this->started_at)->format('%i minutes %s seconds'), 'N/A', false);
    }

    public function getGitRefAttribute()
    {
        return $this->commit['from_ref'] ? 'refs/' . $this->commit['from_ref'] : $this->commit['sha'];
    }

    public function getFingerprintAttribute()
    {
        return sha1($this->server->ssh_host . $this->project->deploy_path);
    }
}
