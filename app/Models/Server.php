<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }
}
