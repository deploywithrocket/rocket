<?php

namespace App\Jobs;

use App\Http\Utils\Discord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Ssh\Ssh as SSH;

trait RemoteJobTrait
{
    protected $ssh;
    protected $scripts;
    protected $xsp;

    public function defineConfig()
    {
        $idk_how_to_use_a_closure = fn ($type, $line) => $this->appendToOutput($line);

        $this->ssh = SSH::create(
            $this->deployment->server->ssh_user,
            $this->deployment->server->ssh_host
        )
        ->usePrivateKey(Storage::path('keys/' . $this->deployment->server->id))
        ->onOutput($idk_how_to_use_a_closure)
        ->disableStrictHostKeyChecking();
    }

    public function beforeHandle()
    {
        $this->deployment->status = 'in_progress';
        $this->deployment->save();

        rescue(function () {
            $gh_client = $this->deployment->project->user->github()->deployments();
            [$user, $repo] = explode('/', $this->deployment->project->repository);

            $this->xsp = $gh_client->create($user, $repo, [
                'ref' => $this->deployment->commit['sha'],
                'target_url' => route('projects.deployments.show', [$this->deployment->project, $this->deployment]),
                'environment' => $this->deployment->project->environment,
            ])['id'];
        });

        if ($this->deployment->project->discord_webhook_url) {
            rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                'title' => 'Deployment started 🔥🔥!',
                'fields' => [
                    ['name' => 'Project', 'value' => $this->deployment->project->name],
                    ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => $this->deployment->commit['from_branch'] . '@' . substr($this->deployment->commit['sha'], 0, 7), 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
            ], 'info'));
        }
    }

    public function afterHandle()
    {
        $this->deployment->status = 'success';
        $this->deployment->save();

        if ($this->xsp ?? null) {
            rescue(function () {
                $gh_client = $this->deployment->project->user->github()->deployments();
                [$user, $repo] = explode('/', $this->deployment->project->repository);

                $gh_client->updateStatus($user, $repo, $this->xsp, [
                    'state' => 'success',
                    'target_url' => route('projects.deployments.show', [$this->deployment->project, $this->deployment]),
                ]);
            });
        }

        if ($this->deployment->project->discord_webhook_url) {
            rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                'title' => 'Application is now live 🚀🚀!',
                'description' => 'See by yourself: [' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')',
                'fields' => [
                    ['name' => 'Project', 'value' => $this->deployment->project->name],
                    ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => $this->deployment->commit['from_branch'] . '@' . substr($this->deployment->commit['sha'], 0, 7), 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
            ], 'success'));
        }
    }

    public function afterHandleFailed()
    {
        $this->deployment->status = 'error';
        $this->deployment->save();

        if ($this->xsp ?? null) {
            rescue(function () {
                $gh_client = $this->deployment->project->user->github()->deployments();
                [$user, $repo] = explode('/', $this->deployment->project->repository);

                $gh_client->updateStatus($user, $repo, $this->xsp, [
                    'state' => 'error',
                    'target_url' => route('projects.deployments.show', [$this->deployment->project, $this->deployment]),
                ]);
            });
        }

        if ($this->deployment->project->discord_webhook_url) {
            rescue(fn () => (new Discord($this->deployment->project->discord_webhook_url))->webhook(null, [
                'title' => 'Your project has failed to deploy 😭😭',
                'description' => '[See the deployment log](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')',
                'fields' => [
                    ['name' => 'Project', 'value' => $this->deployment->project->name],
                    ['name' => 'URL', 'value' => '[' . $this->deployment->project->live_url . '](' . $this->deployment->project->live_url . ')'],
                    ['name' => 'Release', 'value' => '[' . $this->deployment->release . '](' . route('projects.deployments.show', [$this->deployment->project, $this->deployment]) . ')', 'inline' => true],
                    ['name' => 'Commit', 'value' => $this->deployment->commit['from_branch'] . '@' . substr($this->deployment->commit['sha'], 0, 7), 'inline' => true],
                    ['name' => 'Server', 'value' => $this->deployment->server->name, 'inline' => true],
                ],
        ], 'error'));
        }
    }

    public function appendToOutput($buffer)
    {
        $this->deployment->raw_output .= $buffer;
        $this->deployment->save();

        // return DB::update(
        //     'update deployments set raw_output = CONCAT_WS(IFNULL(raw_output, ""), ?), updated_at = ? where id = ?',
        //     [$buffer, now()->format('Y-m-d H:i:s'), $this->deployment->id]
        // );
    }

    public function defineTasks()
    {
        extract($this->deployment->extractVariables());

        $this->scripts['setup:repository'] = "
            export GIT_SSH_COMMAND=\"ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes\"

            if [ -d \"$repo_path\" ]; then
                echo \"Deleting directory $repo_path\" 1>&2
                rm -rf \"$repo_path\"
            fi

            if [ ! -d \"$repository_path\" ]; then
               $cmd_git clone --bare \"$repository_url\" \"$repository_path\"
               $cmd_git --git-dir \"$repository_path\" config advice.detachedHead false
            fi
        ";

        $this->scripts['setup:directories'] = "
            if [ ! -d \"$releases_path\" ]; then
                mkdir -v \"$releases_path\"
            fi

            if [ ! -d \"$shared_path\" ]; then
                mkdir -v \"$shared_path\"
            fi

            if [ ! -d \"$backups_path\" ]; then
                mkdir -v \"$backups_path\"
            fi
        ";

        $this->scripts['assert:commit'] = '';

        if (! $commit) {
            $this->scripts['assert:commit'] = '
                echo "Commit not defined." 1>&2
            ';
        } else {
            $this->scripts['assert:commit'] = "
                echo \"Deploying release $release with tree-ish $commit to $ssh_host...\"
            ";
        }

        $this->scripts['deploy:starting'] = '
            echo "🏃  Starting deployment…"
        ';

        $this->scripts['deploy:check'] = "
            if [ ! -d \"$repository_path\" ]; then
                echo \"Repository path not found.\" 1>&2
                exit 1
            fi

            if [ \"$(git --git-dir $repository_path rev-parse --is-bare-repository)\" != \"true\" ]; then
                echo \"Repository is not bare.\" 1>&2
                exit 1
            fi

            if [ ! -d \"$releases_path\" ]; then
                echo \"Releases path not found.\" 1>&2
                exit 1
            fi

            if [ ! -d \"$shared_path\" ]; then
                echo \"Shared path not found.\" 1>&2
                exit 1
            fi

            if [ ! -d \"$backups_path\" ]; then
                echo \"Backups path not found.\" 1>&2
                exit 1
            fi

            echo \"All checks passed!\"
        ";

        $this->scripts['deploy:started'] = '';

        if ($this->deployment->project->hooks['started'] ?? null) {
            $this->scripts['deploy:started'] .= $this->parseScript($this->deployment->project->hooks['started'], 'started');
        }

        $this->scripts['deploy:provisioning'] = '';

        $this->scripts['deploy:fetch'] = "
            export GIT_SSH_COMMAND=\"ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes\"

            $cmd_git --git-dir \"$repository_path\" remote set-url origin \"$repository_url\"
            $cmd_git --git-dir \"$repository_path\" fetch origin +refs/heads/*:refs/heads/* +refs/tags/*:refs/tags/* --prune
        ";

        $this->scripts['deploy:release'] = "
            echo \"🌀  Cloning repository…\"

            mkdir \"$release_path\"
            cd \"$release_path\"

            $cmd_git --git-dir \"$repository_path\" --work-tree \"$release_path\" checkout -f $commit
            $cmd_git --git-dir \"$repository_path\" --work-tree \"$release_path\" rev-parse HEAD > \"$release_path/REVISION\"
        ";

        $this->scripts['deploy:link'] = '';

        foreach ($linked_dirs as $dir) {
            $this->scripts['deploy:link'] .= "
                echo \"🔗  Linking directory $release_path/$dir to $shared_path/$dir" . "…\"

                mkdir -p `dirname \"$shared_path/$dir\"`

                if [ -d \"$release_path/$dir\" ]; then
                    if [ ! -d \"$shared_path/$dir\" ]; then
                        cp -r \"$release_path/$dir\" \"$shared_path/$dir\"
                    fi

                    rm -rf \"$release_path/$dir\"
                fi

                if [ ! -d \"$shared_path/$dir\" ]; then
                    mkdir \"$shared_path/$dir\"
                fi

                mkdir -p `dirname \"$release_path/$dir\"`

                ln -srfn \"$shared_path/$dir\" \"$release_path/$dir\"
            ";
        }

        foreach ($linked_files as $file) {
            $this->scripts['deploy:link'] .= "
                echo \"🔗  Linking file $release_path/$file to $shared_path/$file" . "…\"

                mkdir -p `dirname \"$shared_path/$file\"`

                if [ -f \"$release_path/$file\" ]; then
                    if [ ! -f \"$shared_path/$file\" ]; then
                        cp \"$release_path/$file\" \"$shared_path/$file\"
                    fi

                    rm -f \"$release_path/$file\"
                fi

                if [ ! -f \"$shared_path/$file\" ]; then
                    touch \"$shared_path/$file\"
                fi

                mkdir -p `dirname \"$release_path/$file\"`

                ln -srfn \"$shared_path/$file\" \"$release_path/$file\"
            ";
        }

        $this->scripts['deploy:copy'] = '';

        foreach ($copied_dirs as $dir) {
            $this->scripts['deploy:copy'] .= "
                echo \"📁  Copying directory $current_path/$dir to $release_path/$dir" . "…\"

                mkdir -p `dirname \"$release_path/$dir\"`

                if [ -d \"$current_path/$dir\" ]; then
                    rsync -a \"$current_path/" . ($dir ? rtrim($dir, '/') . '/' : '') . "\" \"$release_path/$dir\"
                fi
            ";
        }

        foreach ($copied_files as $file) {
            $this->scripts['deploy:copy'] .= "
                echo \"📁  Copying file $current_path/$file to $release_path/$file" . "…\"

                mkdir -p `dirname \"$release_path/$file\"`

                if [ -f \"$current_path/$file\" ]; then
                    rsync -a \"$current_path/$file\" \"$release_path/$file\"
                fi
            ";
        }

        $this->scripts['deploy:dotenv'] = '';

        if ($env_contents = $this->deployment->project->env) {
            $delimiter = 'EOF-ROCKET-ENV';

            $this->scripts['deploy:dotenv'] .= "
                echo \"🖨️  Writing environment file…\"
                cat >$release_path/.env <<$delimiter" . PHP_EOL . $env_contents . PHP_EOL . $delimiter;
        }

        $this->scripts['deploy:composer'] = '';

        foreach (array_unique([$release_path, $assets_path]) as $path) {
            $this->scripts['deploy:composer'] .= "
                cd \"$path\"

                if [ -f \"composer.json\" ]; then
                    echo \"🚚  Running Composer…\";
                    $cmd_composer install $cmd_composer_options --prefer-dist --optimize-autoloader --no-progress --no-interaction
                fi
            ";
        }

        $this->scripts['deploy:npm'] = "
            cd \"$assets_path\"

            if [ -f \"package.json\" ]; then
                if [ -f \"yarn.lock\" ]; then
                    echo \"📦  Running Yarn…\";
                    $cmd_yarn config set ignore-engines true
                    $cmd_yarn install --pure-lockfile --no-progress --non-interactive
                else
                    echo \"📦  Running npm…\";
                    $cmd_npm install
                fi
            fi
        ";

        $this->scripts['deploy:provisioned'] = '';

        if ($this->deployment->project->hooks['provisioned'] ?? null) {
            $this->scripts['deploy:provisioned'] .= $this->parseScript($this->deployment->project->hooks['provisioned'], 'provisioned');
        }

        $this->scripts['deploy:building'] = '';

        $this->scripts['deploy:build'] = "
            cd \"$assets_path\"

            if [ -f \"package.json\" ]; then
                if [ -f \"yarn.lock\" ]; then
                    echo \"🌅  Generating assets…\";
                    $cmd_yarn run production --no-progress
                else
                    echo \"🌅  Generating assets…\";
                    $cmd_npm run production --no-progress
                fi

                echo \"Deleting node_modules directory\"
                rm -rf node_modules
            fi
        ";

        $this->scripts['deploy:built'] = '';

        if ($this->deployment->project->hooks['built'] ?? null) {
            $this->scripts['deploy:built'] .= $this->parseScript($this->deployment->project->hooks['built'], 'built');
        }

        $this->scripts['deploy:publishing'] = '';

        $this->scripts['deploy:symlink'] = "
            echo \"🔥  Linking directory $release_path to $current_path" . "…\"

            ln -srfn \"$release_path\" \"$current_path\"
        ";

        $this->scripts['deploy:publish'] = '';

        $this->scripts['deploy:cronjobs'] = '';

        if ($cron_jobs) {
            $this->scripts['deploy:cronjobs'] .= "
                echo \"⏲  Setting up cron jobs…\"

                FILE=\$(mktemp)
                crontab -l > \$FILE || true

                sed -i '/# ROCKET BEGIN $fingerprint/,/# ROCKET END $fingerprint/d' \$FILE

                echo '# ROCKET BEGIN $fingerprint' >> \$FILE
                echo 'SHELL=\"/bin/bash\"' >> \$FILE
                echo " . escapeshellarg($cron_jobs) . " >> \$FILE
                echo '# ROCKET END $fingerprint' >> \$FILE

                if [ -s \$FILE ]; then
                    crontab \$FILE
                else
                    crontab -r || true
                fi

                rm \$FILE
            ";
        }

        $this->scripts['deploy:published'] = '';

        if ($this->deployment->project->hooks['published'] ?? null) {
            $this->scripts['deploy:published'] .= $this->parseScript($this->deployment->project->hooks['published'], 'published');
        }

        $this->scripts['deploy:finishing'] = '';

        $this->scripts['deploy:cleanup'] = "
            echo \"🗑️  Cleaning up old releases…\"

            cd \"$releases_path\"

            for RELEASE in $(ls -1d * | head -n -$keep_releases); do
                echo \"Deleting old release \$RELEASE\"
                rm -rf \"\$RELEASE\"
            done

            echo \"🚀  Application deployed!\"
        ";

        $this->scripts['deploy:finished'] = '';

        if ($this->deployment->project->hooks['finished'] ?? null) {
            $this->scripts['deploy:finished'] .= $this->parseScript($this->deployment->project->hooks['finished'], 'finished');
        }
    }

    protected function parseScript($script, $hook_name = null)
    {
        extract($this->deployment->extractVariables());

        return Str::of($script)
            ->replace('[[release]]', "\"$release_path\"")
            ->replace('[[sha]]', $commit)
            ->prepend($hook_name ? 'echo "💻  Executing ' . $hook_name . ' hook"' . PHP_EOL : '');
    }
}
