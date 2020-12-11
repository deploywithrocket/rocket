<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Project extends Model
{
    use Concerns\HasUlid;
    use HasApiTokens;
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'linked_dirs' => 'json',
        'linked_files' => 'json',
        'copied_dirs' => 'json',
        'copied_files' => 'json',
        'presets' => 'json',
        'hooks' => 'json',
    ];

    protected $appends = [
        'favicon_url',
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

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    public function latest_deployment()
    {
        return $this->hasOne(Deployment::class)->latest();
    }

    public function pings()
    {
        return $this->hasMany(Ping::class);
    }

    public function getFaviconUrlAttribute()
    {
        return rescue(function () {
            return 'https://icons.duckduckgo.com/ip3/' . parse_url($this->live_url, PHP_URL_HOST) . '.ico';
        });
    }

    public function getRepositoryUrlAttribute()
    {
        if ($github_account = $this->user->github_account) {
            return ''
                . 'https://'
                . $github_account->provider_user_name . ':' . $github_account->token
                . '@github.com'
                . '/' . $this->repository . '.git';
        }

        return 'https://github.com/' . $this->repository . '.git';
    }
}
