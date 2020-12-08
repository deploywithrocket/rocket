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

        Server::firstOrCreate([
            'name' => 'universe/sc1',
            'ssh_user' => 'sc1mgkprod',
            'ssh_host' => 'sc1mgkprod.universe.wf',
            'status' => 'disconnected',
        ]);

        Server::firstOrCreate([
            'name' => 'universe/sc2',
            'ssh_user' => 'sc2mgkprod',
            'ssh_host' => 'sc2mgkprod.universe.wf',
            'status' => 'disconnected',
        ]);

        $server = Server::firstOrCreate([
            'name' => 'o2switch/saucisson',
            'ssh_user' => 'mgkprod',
            'ssh_host' => 'saucisson.o2switch.net',
            'status' => 'disconnected',
        ]);

        $server->projects()->firstOrCreate([
            'name' => 'mgkprod/rocket',
            'repository' => 'mgkprod/rocket',
            'branch' => 'main',
            'deploy_path' => '/home/mgkprod/rocket.mgk.dev',
            'live_url' => 'https://rocket.mgk.dev',
            'linked_dirs' => ['storage/app', 'storage/framework', 'storage/logs'],
            'copied_dirs' => ['node_modules', 'vendor'],
        ]);
    }
}
