<?php

namespace App\Models;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;

class User extends Authenticatable
{
    use Notifiable;
    use Concerns\HasUlid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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
