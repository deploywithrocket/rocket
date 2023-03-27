<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDeploymentController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home')->name('index');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/auth/github', [HomeController::class, 'redirectToProvider'])->name('auth.github');
    Route::get('/auth/github/callback', [HomeController::class, 'handleProviderCallback'])->name('auth.github.callback');

    Route::group([
        'prefix' => 'servers',
        'as' => 'servers.',
    ], function () {
        Route::get('/', [ServerController::class, 'index'])->name('index');
        Route::get('/create', [ServerController::class, 'create'])->name('create');
        Route::post('/', [ServerController::class, 'store'])->name('store');
        Route::get('/{server}/connection', [ServerController::class, 'connection'])->name('connection');
        Route::post('/{server}/connection/test', [ServerController::class, 'connectionTest'])->name('connection.test');
        Route::get('/{server}/edit', [ServerController::class, 'edit'])->name('edit');
        Route::put('/{server}', [ServerController::class, 'update'])->name('update');
        Route::delete('/{server}', [ServerController::class, 'destroy'])->name('destroy');
    });

    Route::group([
        'prefix' => 'projects',
        'as' => 'projects.',
    ], function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::get('/{project}/edit/common', [ProjectController::class, 'editCommon'])->name('edit.common');
        Route::put('/{project}/common', [ProjectController::class, 'updateCommon'])->name('update.common');
        Route::get('/{project}/edit/env-file', [ProjectController::class, 'editEnvFile'])->name('edit.env-file');
        Route::put('/{project}/env-file', [ProjectController::class, 'updateEnvFile'])->name('update.env-file');
        Route::get('/{project}/edit/hooks', [ProjectController::class, 'editHooks'])->name('edit.hooks');
        Route::put('/{project}/hooks', [ProjectController::class, 'updateHooks'])->name('update.hooks');
        Route::get('/{project}/edit/cron-jobs', [ProjectController::class, 'editCronJobs'])->name('edit.cron-jobs');
        Route::put('/{project}/cron-jobs', [ProjectController::class, 'updateCronJobs'])->name('update.cron-jobs');
        Route::get('/{project}/edit/shared', [ProjectController::class, 'editShared'])->name('edit.shared');
        Route::put('/{project}/shared', [ProjectController::class, 'updateShared'])->name('update.shared');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
        Route::get('/{project}/webhook/discord/test', [ProjectController::class, 'testDiscordWebhook'])->name('test-discord-webhook');
        Route::get('/{project}/deployments/create', [ProjectDeploymentController::class, 'create'])->name('deployments.create');
        Route::post('/{project}/deployments', [ProjectDeploymentController::class, 'store'])->name('deployments.store');
        Route::get('/{project}/deployments', [ProjectDeploymentController::class, 'index'])->name('deployments.index');
        Route::get('/{project}/deployments/{deployment}', [ProjectDeploymentController::class, 'show'])->name('deployments.show');
    });
});
