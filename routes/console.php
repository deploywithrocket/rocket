<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('users:create', function () {
    $this->info('Creating new user');

    $user = new User();
    $user->name = $this->ask('Name ?', null);
    $user->email = $this->ask('Email ?', null);
    $user->password = Hash::make($this->secret('Password ?'));
    $user->email_verified_at = now();
    $user->remember_token = Str::random(10);
    $user->save();

    $this->line('User created with id: ' . $user->id);
});

Artisan::command('fix:keys', function () {
    $files = Storage::allFiles('keys');

    foreach ($files as $file) {
        File::chmod(Storage::path($file), 0600);
    }

    // File::chmod(Storage::path('keys'), 0700);
});
