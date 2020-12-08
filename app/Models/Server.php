<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

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
}
