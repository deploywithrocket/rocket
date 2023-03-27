<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Server extends Model implements Searchable
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    protected $hidden = [
        'ssh_host',
        'ssh_port',
        'ssh_user',
        'cmd_git',
        'cmd_npm',
        'cmd_yarn',
        'cmd_php',
        'cmd_composer',
        'cmd_composer_options',
    ];

    protected static function booted()
    {
        if (auth()->user()) {
            static::addGlobalScope('user', function (Builder $builder) {
                $builder->where('user_id', '=', auth()->id());
            });
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->name,
            route('servers.edit', $this)
        );
    }
}
