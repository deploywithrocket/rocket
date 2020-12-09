<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

    protected $casts = [
        'commit' => 'json',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected $appends = [
        'duration',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function getDurationAttribute()
    {
        return rescue(fn () => $this->ended_at->diff($this->started_at)->format('%i minutes %s seconds'), 'N/A', false);
    }

    public function getRelativePath($basePath, $relativePath = '')
    {
        return rtrim($basePath, '/') . ($relativePath ? '/' . ltrim($relativePath, '/') : '');
    }

    public function getDeployPath($path = '')
    {
        return $this->getRelativePath($this->project->deploy_path, $path);
    }

    public function getReleasePath($release, $path = '')
    {
        return $this->getRelativePath($this->getRelativePath($this->getDeployPath('releases'), $release), $path);
    }

    public function buildFingerprint()
    {
        return sha1($this->server->ssh_host . $this->project->deploy_path);
    }

    public function extractVariables()
    {
        $repository_url = 'https://github.com/' . $this->project->repository . '.git';

        $github_account = $this->project->user->github_account;

        if ($github_account) {
            $repository_url = ''
                . 'https://'
                . $github_account->provider_user_name . ':' . $github_account->token
                . '@github.com'
                . '/' . $this->project->repository . '.git';
        }

        $ref = $this->commit['from_ref'] ? 'refs/' . $this->commit['from_ref'] : $this->commit['sha'];

        return [
            'release' => $this->release,
            'ref' => $ref,
            'sha' => $this->commit['sha'],
            'ssh_host' => $this->server->ssh_host,
            'ssh_user' => $this->server->ssh_user,
            'deploy_path' => $this->project->deploy_path,
            'repository_url' => $repository_url,
            'linked_files' => $this->project->linked_files ?? [],
            'linked_dirs' => $this->project->linked_dirs ?? [],
            'copied_files' => $this->project->copied_files ?? [],
            'copied_dirs' => $this->project->copied_dirs ?? [],
            'cron_jobs' => $this->project->cron_jobs ?? null,
            'keep_releases' => $this->project->keep_releases ?? 5,
            'cmd_git' => $this->server->cmd_git ?? 'git',
            'cmd_npm' => $this->server->cmd_npm ?? 'npm',
            'cmd_yarn' => $this->server->cmd_npm ?? 'yarn',
            'cmd_php' => $this->server->cmd_php ?? 'php',
            'cmd_composer' => $this->server->cmd_composer ?? 'composer',
            'cmd_composer_options' => $this->server->cmd_composer_options ?? '--no-dev',
            'fingerprint' => $this->buildFingerprint(),

            // Variables computed internally that defined paths
            'project_path' => $this->getDeployPath(),
            'repository_path' => $this->getDeployPath('repository'),
            'current_path' => $this->getDeployPath('current'),
            'releases_path' => $this->getDeployPath('releases'),
            'release_path' => $this->getReleasePath($this->release),
            'assets_path' => $this->getReleasePath($this->release),
            'shared_path' => $this->getDeployPath('shared'),
        ];
    }
}
