<?php

namespace App\Utils;

use App\Models\Deployment;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class ShellScriptRenderer
{
    public Deployment $deployment;
    public array $data;

    public function __construct(Deployment $deployment, $data = [])
    {
        $this->deployment = $deployment;
        $this->data = $data;

        view()->addNamespace('shellscripts', resource_path('shellscripts'));
        view()->addExtension('blade.sh', 'blade');

        $this->prepareVariables();

        return $this;
    }

    public function render($view, $data = [])
    {
        if (
            Str::startsWith($view, 'hooks.')
            && $hook = $this->deployment->project->hooks[$hook_name = str_replace('hooks.', '', $view)] ?? null
        ) {
            return
                view('shellscripts::hooks.message', compact('hook_name'))->render() . PHP_EOL
                . $this->view_from_text($hook, array_merge($this->data, $data));
        }

        return view('shellscripts::' . $view, array_merge($this->data, $data))->render();
    }

    protected function prepareVariables()
    {
        $data = [
            'fingerprint' => $this->deployment->fingerprint,
            'release' => $this->deployment->release,
            'ref' => $this->deployment->git_ref,
            'sha' => $this->deployment->commit['sha'],
            'ssh_host' => $this->deployment->server->ssh_host,
            'ssh_user' => $this->deployment->server->ssh_user,
            'repository_url' => $this->deployment->project->repository_url,
            'keep_releases' => $this->deployment->project->keep_releases ?? 5,
        ];

        $user = [
            'cron_jobs' => $this->deployment->project->cron_jobs ?? null,
            'env_contents' => $this->deployment->project->env ?? null,
            'cmd_composer_options' => $this->deployment->server->cmd_composer_options ?? '--prefer-dist --optimize-autoloader --no-progress --no-interaction',
        ];

        $paths = [
            'deploy_path' => $this->deployment->project->deploy_path,
            'repository_path' => $this->path($this->deployment->project->deploy_path, '/repository'),
            'current_path' => $this->path($this->deployment->project->deploy_path, '/current'),
            'releases_path' => $this->path($this->deployment->project->deploy_path, '/releases'),
            'release_path' => $this->path($this->deployment->project->deploy_path, '/releases/' . $this->deployment->release),
            'shared_path' => $this->path($this->deployment->project->deploy_path, '/shared'),
            'env_path' => $this->path($this->deployment->project->deploy_path, '/releases/' . $this->deployment->release . '/.env'),
        ];

        $linked_dirs = $linked_files = [];

        foreach ($this->deployment->project->linked_dirs ?? [] as $dir) {
            $linked_dirs[$this->path($paths['release_path'], $dir)] = $this->path($paths['shared_path'], $dir);
        }

        foreach ($this->deployment->project->linked_files ?? [] as $file) {
            $linked_files[$this->path($paths['release_path'], $file)] = $this->path($paths['shared_path'], $file);
        }

        $commands = [
            'cmd_git' => $this->deployment->server->cmd_git ?? 'git',
            'cmd_npm' => $this->deployment->server->cmd_npm ?? 'npm',
            'cmd_yarn' => $this->deployment->server->cmd_npm ?? 'yarn',
            'cmd_php' => $this->deployment->server->cmd_php ?? 'php',
            'cmd_composer' => $this->deployment->server->cmd_composer ?? 'composer',
        ];

        $this->data = array_merge(
            $this->data,
            $user,
            [
                'linked_dirs' => $this->escape($linked_dirs),
                'linked_files' => $this->escape($linked_files),
            ],
            $data,
            $this->escape($paths),
            $this->escape($commands),
        );

        return $this;
    }

    protected function escape($value)
    {
        if (is_array($value)) {
            return collect($value)
            ->map(fn ($value) => escapeshellarg($value))
            ->toArray();
        } else {
            return escapeshellarg($value);
        }
    }

    protected function path(...$paths)
    {
        $is_relative = ! Str::startsWith($paths[0], '/');

        return (! $is_relative ? '/' : '')
            . collect($paths)
                ->map(fn ($value) => trim($value, '/'))
                ->join('/');
    }

    /**
     * @see https://laracasts.com/discuss/channels/general-discussion/render-template-from-blade-template-in-database?reply=175518
     */
    protected function view_from_text($blade, $__data)
    {
        $__php = Blade::compileString($blade);

        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);

        try {
            eval('?' . '>' . $__php);
        } catch (\Throwable $th) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw $e;
        }

        return ob_get_clean();
    }
}
