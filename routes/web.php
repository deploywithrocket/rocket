<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDeploymentController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::redirect('/', '/home')->name('index');
Route::inertia('home', 'home')->name('home')->middleware('auth');

Route::group(['middleware' => ['auth']], function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::group([
        'prefix' => 'servers',
        'as' => 'servers.',
    ], function () {
        Route::get('/', [ServerController::class, 'index'])->name('index');
        Route::get('/create', [ServerController::class, 'create'])->name('create');
        Route::post('/', [ServerController::class, 'store'])->name('store');
        Route::get('/{server}', [ServerController::class, 'show'])->name('show');
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
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');

        Route::get('/{project}/setup', [ProjectController::class, 'setup'])->name('setup');
        Route::get('/{project}/deploy', [ProjectController::class, 'deploy'])->name('deploy');

        Route::get('/{project}/{deployment}', [ProjectDeploymentController::class, 'show'])->name('deployments.show');
    });
});
