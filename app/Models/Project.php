<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use Concerns\HasUlid;

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
        return $this->hasOne(Deployment::class)->orderBy('created_at', 'DESC');
    }

    public function getFaviconUrlAttribute()
    {
        return rescue(function () {
            return 'https://icons.duckduckgo.com/ip3/' . parse_url($this->live_url, PHP_URL_HOST) . '.ico';
        });
    }
}
