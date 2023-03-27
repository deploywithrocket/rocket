<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function social_accounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function github_account()
    {
        return $this
            ->hasOne(SocialAccount::class)
            ->where('provider', 'github');
    }

    public function github()
    {
        $github_account = $this->github_account;

        if (! $github_account) {
            return null;
        }

        Config::set('github.connections.main.token', $github_account->token);

        return GitHub::connection('main');
    }
}
