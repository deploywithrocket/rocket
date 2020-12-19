<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Utils\DotEnvInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AppController extends Controller
{
    public function show()
    {
        $dotenv = new DotEnvInterface();

        $current_env = [
            'name' => $dotenv->get('APP_NAME'),
            'url' => $dotenv->get('APP_URL'),
        ];

        return inertia('install/app/show', compact('current_env'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'url' => ['required'],
        ]);

        $dotenv = new DotEnvInterface();

        $dotenv->set('APP_NAME', $request->name);
        $dotenv->set('APP_URL', $request->url);

        $dotenv->save();

        Artisan::call('config:clear');

        return redirect()->route('install.user.show');
    }
}
