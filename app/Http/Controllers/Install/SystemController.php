<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function show()
    {
        $min_php_version = [70400 => '^7.4'];

        if (PHP_VERSION_ID >= 70400) {
            $min_php_version = [];
        }

        $extensions = collect(['dom', 'fileinfo', 'filter', 'json', 'libxml', 'mbstring', 'openssl', 'pcre', 'phar', 'posix', 'tokenizer', 'xml', 'xmlwriter']);
        $extensions = $extensions->reject(fn ($ext) => extension_loaded($ext));

        $permissions = collect(['storage' => '0775', 'bootstrap/cache' => '0775', '.env' => '0775']);
        $permissions = $permissions->reject(fn ($permission, $dir) => is_writable(base_path($dir)));

        if (count($min_php_version) + count($extensions) + count($permissions) == 0) {
            return redirect()->route('install.database.show');
        }

        return inertia('install/system/show', compact('min_php_version', 'extensions', 'permissions'));
    }
}
