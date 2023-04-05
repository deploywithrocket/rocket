<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentTask extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    protected $casts = [
        'commands' => 'json',
    ];

    protected $hidden = [
        'commands',
        'output',
    ];

    public function deployment()
    {
        return $this->belongsTo(Deployment::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
