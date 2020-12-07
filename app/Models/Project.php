<?php

namespace App\Models;

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
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }
}
