<?php

use App\Models\Server;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api', 'as' => 'api.'], function () {
});

Route::get('/servers/{server}/connect', function (Server $server) {
    $key = Storage::get('keys/' . $server->id . '.pub');

    echo '#!/bin/bash' . PHP_EOL
        . "echo \"Adding $server->name key for user \$(whoami)\"" . PHP_EOL
        . "echo \"$key\" >> ~/.ssh/authorized_keys" . PHP_EOL
        . 'echo "Key added!"';

    exit;
})->name('api.servers.connect');
