<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Utils\DotEnvInterface;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class GitHubController extends Controller
{
    public function show()
    {
        $dotenv = new DotEnvInterface();

        $current_env = [
            'client_id' => $dotenv->get('GITHUB_CLIENT_ID'),
            'client_secret' => $dotenv->get('GITHUB_CLIENT_SECRET'),
        ];

        return inertia('install/github/show', compact('current_env'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'client_id' => ['required'],
            'client_secret' => ['required'],
        ]);

        $dotenv = new DotEnvInterface();

        $dotenv->set('GITHUB_CLIENT_ID', $request->client_id);
        $dotenv->set('GITHUB_CLIENT_SECRET', $request->client_secret);

        $dotenv->save();

        Artisan::call('config:clear');

        // Override for this request
        Config::set('github.connections.app.clientId', $request->client_id);
        Config::set('github.connections.app.clientSecret', $request->client_secret);

        try {
            GitHub::connection('app')->repositories()->show('deploywithrocket', 'core');
        } catch (\Throwable $th) {
            return back()->with('error', 'The credentials provided seems incoorect');
        }

        Storage::put('installed', time());

        return redirect()->route('home');
    }
}
