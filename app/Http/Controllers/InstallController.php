<?php

namespace App\Http\Controllers;

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
        $env = [
            'connection' => env('DB_CONNECTION'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ];

        return inertia('install/database', compact('env'));
    }
}
