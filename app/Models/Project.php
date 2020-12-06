<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
