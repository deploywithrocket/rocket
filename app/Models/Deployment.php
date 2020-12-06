<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
