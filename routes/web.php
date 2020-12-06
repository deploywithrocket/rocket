<?php

use App\Http\Controllers\Auth\LoginController;
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

Route::group([
    'middleware' => ['auth'],
], function () {
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::group([
        'prefix' => 'campaigns',
        'as' => 'next.campaigns.',
    ], function () {
        Route::get('/', 'Next\CampaignController@index')->name('index');
        Route::get('/create', 'Next\CampaignController@create')->name('create');
        Route::post('/', 'Next\CampaignController@store')->name('store');
        Route::get('/{id}', 'Next\CampaignController@show')->name('show');
        Route::get('/{id}/edit', 'Next\CampaignController@edit')->name('edit');
        Route::put('/{id}', 'Next\CampaignController@update')->name('update');
        Route::delete('/{id}', 'Next\CampaignController@destroy')->name('destroy');
        Route::get('/{id}/prepare', 'Next\CampaignController@prepare')->name('prepare');
        Route::get('/{id}/send', 'Next\CampaignController@send')->name('send');
    });
});
