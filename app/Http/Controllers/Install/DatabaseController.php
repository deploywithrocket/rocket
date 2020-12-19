<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Utils\DotEnvInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends Controller
{
    public function show()
    {
        $connections = [
            'mysql' => 'mysql',
            'pgsql' => 'pgsql',
            'sqlsrv' => 'sqlsrv',
        ];

        $dotenv = new DotEnvInterface();

        $current_env = [
            'connection' => $dotenv->get('DB_CONNECTION'),
            'host' => $dotenv->get('DB_HOST'),
            'port' => $dotenv->get('DB_PORT'),
            'database' => $dotenv->get('DB_DATABASE'),
            'username' => $dotenv->get('DB_USERNAME'),
            'password' => $dotenv->get('DB_PASSWORD'),
        ];

        return inertia('install/database/show', compact('connections', 'current_env'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'connection' => ['required'],
            'host' => ['required'],
            'port' => ['required'],
            'database' => ['required'],
            'username' => ['required'],
        ]);

        $dotenv = new DotEnvInterface();

        $dotenv->set('DB_CONNECTION', $request->connection);
        $dotenv->set('DB_HOST', $request->host);
        $dotenv->set('DB_PORT', $request->port);
        $dotenv->set('DB_DATABASE', $request->database);
        $dotenv->set('DB_USERNAME', $request->username);
        $dotenv->set('DB_PASSWORD', $request->password);

        $dotenv->save();

        Artisan::call('config:clear');

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

        return redirect()->route('install.app.show');
    }
}
