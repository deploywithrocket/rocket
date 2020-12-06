<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        URL::forceScheme('https');

        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, config('app.locale'));

        // Version
        $version = '';

        try {
            $version = 'v' . file_get_contents(config_path('.version'));
        } catch (Throwable $e) {
        }

        Inertia::share('version', $version);

        // Flash messages
        Inertia::share('flash', function () {
            return [
                'success' => Session::get('success'),
                'warning' => Session::get('warning'),
                'error' => Session::get('error'),
            ];
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
