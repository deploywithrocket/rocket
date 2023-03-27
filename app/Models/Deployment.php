<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Deployment extends Model implements Searchable
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    protected $casts = [
        'commit' => 'json',
        'raw_output' => 'json',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected $appends = [
        'duration',
    ];

    protected $hidden = [
        'raw_output',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
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

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->release,
            route('projects.deployments.show', [$this->project, $this])
        );
    }
}
