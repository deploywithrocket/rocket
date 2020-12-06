<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectController;
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
        Route::get('/{server}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{server}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{server}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{server}', [ProjectController::class, 'destroy'])->name('destroy');
    });
});
