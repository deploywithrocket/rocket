<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;

class DotEnvInterface
{
    protected $path;
    protected $contents;

    public function __construct($file = '.env')
    {
        $this->path = base_path($file);
        $this->contents = File::get($this->path);
    }

    public function get($key)
    {
        return env($key);
    }

    public function set($key, $value = '')
    {
        $this->contents = preg_replace('/(' . $key . ')=(.*)/', '$1=' . escapeshellarg($value), $this->contents);

        return $this;
    }

    public function save()
    {
        File::put($this->path, $this->contents);

        return $this;
    }
}
