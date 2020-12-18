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
        return rescue(function () use ($key) {
            preg_match('/' . $key . '=(.*)/', $this->contents, $matches);

            return $matches[1];
        }, null, false);
    }

    public function set($key, $value = '')
    {
        $this->contents = preg_replace('/(' . $key . ')=(.*)/', '$1=' . $value, $this->contents);

        return $this;
    }

    public function save()
    {
        File::put($this->path, $this->contents);

        return $this;
    }
}
