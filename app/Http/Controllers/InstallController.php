<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\DotEnvInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class InstallController extends Controller
{
    public function index()
    {
        return inertia('install/index');
    }

    public function system()
    {
        $min_php_version = [70400 => '^7.4'];

        if (PHP_VERSION_ID >= 70400) {
            $min_php_version = [];
        }

        $extensions = collect(['dom', 'fileinfo', 'filter', 'json', 'libxml', 'mbstring', 'openssl', 'pcre', 'phar', 'posix', 'tokenizer', 'xml', 'xmlwriter']);
        $extensions = $extensions->reject(fn ($ext) => extension_loaded($ext));

        $permissions = collect(['storage' => '0775', 'bootstrap/cache' => '0775']);
        $permissions = $permissions->reject(fn ($permission, $dir) => is_writable(base_path($dir)));

        if (count($min_php_version) + count($extensions) + count($permissions) == 0) {
            return redirect()->route('install.database');
        }

        return inertia('install/system', compact('min_php_version', 'extensions', 'permissions'));
    }

    public function database()
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

        return inertia('install/database', compact('connections', 'current_env'));
    }

    public function submitDatabase(Request $request)
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

        return redirect()->route('install.app');
    }

    public function app()
    {
        $dotenv = new DotEnvInterface();

        $current_env = [
            'name' => $dotenv->get('APP_NAME'),
            'url' => $dotenv->get('APP_URL'),
        ];

        return inertia('install/app', compact('current_env'));
    }

    public function submitApp(Request $request)
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

        return redirect()->route('install.user');
    }

    public function user()
    {
        return inertia('install/user');
    }

    public function submitUser(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('install.github');
    }

    public function github()
    {
        $dotenv = new DotEnvInterface();

        $current_env = [
            'client_id' => $dotenv->get('GITHUB_CLIENT_ID'),
            'client_secret' => $dotenv->get('GITHUB_CLIENT_SECRET'),
        ];

        return inertia('install/github', compact('current_env'));
    }

    public function submitGithub(Request $request)
    {
        $request->validate([
            'client_id' => ['required'],
            'client_secret' => ['required'],
        ]);

        $dotenv = new DotEnvInterface();

        $dotenv->set('GITHUB_CLIENT_ID', $request->client_id);
        $dotenv->set('GITHUB_CLIENT_SECRET', $request->client_secret);

        $dotenv->save();

        Storage::put('installed', time());

        return redirect()->route('home');
    }
}
