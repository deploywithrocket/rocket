<?php

namespace Database\Seeders;

use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'name' => 'MGK',
            'email' => 'sr@mgk.dev',
        ], [
            'password' => \Hash::make('1234'),
        ]);

        $server = Server::firstOrCreate([
            'name' => 'Saucisson',
            'user' => 'mgkprod',
            'address' => 'saucisson.o2switch.org',
        ]);

        $server->projects()->firstOrCreate([
            'name' => 'mgkprod/rocket',
            'repository_url' => 'git@github.com:mgkprod/rocket.git',
            'deploy_path' => '/home/mgkprod/rocket.mgk.dev',
            'health_url' => 'https://rocket.mgk.dev',
        ]);
    }
}
