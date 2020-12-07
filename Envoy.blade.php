@setup
    extract(json_decode(file_get_contents(__DIR__ . '/storage/app/' . $deptrace), true));
@endsetup

@servers(['web' => $server_string])

@task('assert:commit')
    @if (! $commit)
        echo "Commit not defined." 1>&2
    @else
        echo "Deploying release {{ $release }} with tree-ish {{ $commit }} to {{ $ssh_host }}..."
    @endif
@endtask

@task('preflight')
    true
@endtask

@macro('backups')
    backups:list
@endmacro

@task('backups:list')
    ls -1 "{{ $backups_path }}"
@endtask

@macro('deploy')
    preflight
    assert:commit
    deploy:starting
        deploy:check
        deploy:backup
    deploy:started
    deploy:provisioning
        deploy:fetch
        deploy:release
        deploy:link
        deploy:copy
        deploy:composer
        deploy:npm
    deploy:provisioned
    deploy:building
        deploy:build
    deploy:built
    deploy:publishing
        deploy:symlink
        deploy:publish
        deploy:cronjobs
    deploy:published
    deploy:finishing
        deploy:cleanup
    deploy:finished
@endmacro

@task('deploy:starting')
    true
@endtask

@task('deploy:check')
    if [ ! -d "{{ $repository_path }}" ]; then
        echo "Repository path not found." 1>&2
        exit 1
    fi

    if [ "$(git --git-dir {{ $repository_path }} rev-parse --is-bare-repository)" != "true" ]; then
        echo "Repository is not bare." 1>&2
        exit 1
    fi

    if [ ! -d "{{ $releases_path }}" ]; then
        echo "Releases path not found." 1>&2
        exit 1
    fi

    if [ ! -d "{{ $shared_path }}" ]; then
        echo "Shared path not found." 1>&2
        exit 1
    fi

    if [ ! -d "{{ $backups_path }}" ]; then
        echo "Backups path not found." 1>&2
        exit 1
    fi

    echo "All checks passed!"
@endtask

@task('deploy:backup')
    true
@endtask

@task('deploy:started')
    true
@endtask

@task('deploy:provisioning')
    true
@endtask

@task('deploy:fetch')
    export GIT_SSH_COMMAND="ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes"

    {{ $cmd_git }} --git-dir "{{ $repository_path }}" remote set-url origin "{{ $repository_url }}"
    {{ $cmd_git }} --git-dir "{{ $repository_path }}" fetch origin +refs/heads/*:refs/heads/* +refs/tags/*:refs/tags/* --prune
@endtask

@task('deploy:release')
    mkdir "{{ $release_path }}"
    cd "{{ $release_path }}"

    {{ $cmd_git }} --git-dir "{{ $repository_path }}" --work-tree "{{ $release_path }}" checkout -f {{ $commit }}
    {{ $cmd_git }} --git-dir "{{ $repository_path }}" --work-tree "{{ $release_path }}" rev-parse HEAD > "{{ $release_path }}/REVISION"
@endtask

@task('deploy:link')
    @run('deploy:link:dirs')
    @run('deploy:link:files')
@endtask

@task('deploy:link:dirs')
    @foreach ($linked_dirs as $dir)
        echo "Linking directory {{ $release_path }}/{{ $dir }} to {{ $shared_path }}/{{ $dir }}"

        mkdir -p `dirname "{{ $shared_path }}/{{ $dir }}"`

        if [ -d "{{ $release_path }}/{{ $dir }}" ]; then
            if [ ! -d "{{ $shared_path }}/{{ $dir }}" ]; then
                cp -r "{{ $release_path }}/{{ $dir }}" "{{ $shared_path }}/{{ $dir }}"
            fi

            rm -rf "{{ $release_path }}/{{ $dir }}"
        fi

        if [ ! -d "{{ $shared_path }}/{{ $dir }}" ]; then
            mkdir "{{ $shared_path }}/{{ $dir }}"
        fi

        mkdir -p `dirname "{{ $release_path }}/{{ $dir }}"`

        ln -srfn "{{ $shared_path }}/{{ $dir }}" "{{ $release_path }}/{{ $dir }}"
    @endforeach
@endtask

@task('deploy:link:files')
    @foreach ($linked_files as $file)
        echo "Linking file {{ $release_path }}/{{ $file }} to {{ $shared_path }}/{{ $file }}"

        mkdir -p `dirname "{{ $shared_path }}/{{ $file }}"`

        if [ -f "{{ $release_path }}/{{ $file }}" ]; then
            if [ ! -f "{{ $shared_path }}/{{ $file }}" ]; then
                cp "{{ $release_path }}/{{ $file }}" "{{ $shared_path }}/{{ $file }}"
            fi

            rm -f "{{ $release_path }}/{{ $file }}"
        fi

        if [ ! -f "{{ $shared_path }}/{{ $file }}" ]; then
            touch "{{ $shared_path }}/{{ $file }}"
        fi

        mkdir -p `dirname "{{ $release_path }}/{{ $file }}"`

        ln -srfn "{{ $shared_path }}/{{ $file }}" "{{ $release_path }}/{{ $file }}"
    @endforeach
@endtask

@task('deploy:copy')
    @run('deploy:copy:dirs')
    @run('deploy:copy:files')
@endtask

@task('deploy:copy:dirs')
    @foreach ($copied_dirs as $dir)
        echo "Copying directory {{ $current_path }}/{{ $dir }} to {{ $release_path }}/{{ $dir }}"

        mkdir -p `dirname "{{ $release_path }}/{{ $dir }}"`

        if [ -d "{{ $current_path }}/{{ $dir }}" ]; then
            rsync -a "{{ $current_path }}/{{ $dir ? rtrim($dir, '/') .'/' : '' }}" "{{ $release_path }}/{{ $dir }}"
        fi
    @endforeach
@endtask

@task('deploy:copy:files')
    @foreach ($copied_files as $file)
        echo "Copying file {{ $current_path }}/{{ $file }} to {{ $release_path }}/{{ $file }}"

        mkdir -p `dirname "{{ $release_path }}/{{ $file }}"`

        if [ -f "{{ $current_path }}/{{ $file }}" ]; then
            rsync -a "{{ $current_path }}/{{ $file }}" "{{ $release_path }}/{{ $file }}"
        fi
    @endforeach
@endtask

@task('deploy:composer')
    @foreach (array_unique([$release_path, $assets_path]) as $path)
        cd "{{ $path }}"

        if [ -f "composer.json" ]; then
            {{ $cmd_composer }} install {{ $cmd_composer_options }} --prefer-dist --optimize-autoloader --no-progress --no-interaction
        fi
    @endforeach
@endtask

@task('deploy:npm')
    cd "{{ $assets_path }}"

    if [ -f "package.json" ]; then
        if [ -f "yarn.lock" ]; then
            {{ $cmd_yarn }} install --pure-lockfile --no-progress --non-interactive
        else
            {{ $cmd_npm }} install
        fi
    fi
@endtask

@task('deploy:provisioned')
    true
@endtask

@task('deploy:building')
    true
@endtask

@task('deploy:build')
    cd "{{ $assets_path }}"

    if [ -f "package.json" ]; then
        if [ -f "yarn.lock" ]; then
            {{ $cmd_yarn }} run production --no-progress
        else
            {{ $cmd_npm }} run production --no-progress
        fi
    fi
@endtask

@task('deploy:built')
    true
@endtask

@task('deploy:publishing')
    true
@endtask

@task('deploy:symlink')
    echo "Linking directory {{ $release_path }} to {{ $current_path }}"

    ln -srfn "{{ $release_path }}" "{{ $current_path }}"
@endtask

@task('deploy:publish')
    cd "{{ $releasePath }}"

    php artisan down
    php artisan migrate --force

    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link

    php artisan up
@endtask

@task('deploy:cronjobs')
    FILE=$(mktemp)
    crontab -l > $FILE || true

    sed -i '/# ROCKET BEGIN {{ $fingerprint }}/,/# ROCKET END {{ $fingerprint }}/d' $FILE

    @if (is_array($cron_jobs) && count($cron_jobs) > 0)
        echo '# ROCKET BEGIN {{ $fingerprint }}' >> $FILE
        echo 'SHELL="/bin/bash"' >> $FILE
        @foreach ($cron_jobs as $cron_job)
            echo {{ escapeshellarg($cron_job) }} >> $FILE
        @endforeach
        echo '# ROCKET END {{ $fingerprint }}' >> $FILE
    @endif

    if [ -s $FILE ]; then
        crontab $FILE
    else
        crontab -r || true
    fi

    rm $FILE
@endtask

@task('deploy:published')
    true
@endtask

@task('deploy:finishing')
    true
@endtask

@task('deploy:cleanup')
    cd "{{ $releases_path }}"

    for RELEASE in $(ls -1d * | head -n -{{ $keep_releases }}); do
        echo "Deleting old release $RELEASE"
        rm -rf "$RELEASE"
    done
@endtask

@task('deploy:finished')
    true
@endtask

@macro('releases')
    releases:list
@endmacro

@task('releases:list')
    ls -1 "{{ $releases_path }}"
@endtask

@macro('rollback')
    deploy:starting
        deploy:check
        deploy:backup
    deploy:started
    deploy:publishing
        rollback:symlink
        deploy:publish
        deploy:cronjobs
    deploy:published
    deploy:finishing
    deploy:finished
@endmacro

@task('rollback:symlink')
    @if (isset($release))
        RELEASE="{{ $release }}"
    @else
        cd "{{ $releases_path }}"
        RELEASE=`ls -1d */ | head -n -1 | tail -n 1 | sed "s/\/$//"`
    @endif

    if [ ! -d "{{ $releases_path }}/$RELEASE" ]; then
        echo "Release $RELEASE not found. Could not rollback."
        exit 1
    fi

    echo "Linking directory {{ $releases_path }}/$RELEASE to {{ $current_path }}"

    ln -srfn "{{ $releases_path }}/$RELEASE" "{{ $current_path }}"
@endtask

@macro('setup')
    setup:repository
    setup:directories
@endmacro

@task('setup:repository')
    export GIT_SSH_COMMAND="ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes"

    if [ -d "{{ $repo_path }}" ]; then
        echo "Deleting directory {{ $repo_path }}" 1>&2
        rm -rf "{{ $repo_path }}"
    fi

    if [ ! -d "{{ $repository_path }}" ]; then
        {{ $cmd_git }} clone --bare "{{ $repository_url }}" "{{ $repository_path }}"
        {{ $cmd_git }} --git-dir "{{ $repository_path }}" config advice.detachedHead false
    fi
@endtask

@task('setup:directories')
    if [ ! -d "{{ $releases_path }}" ]; then
        mkdir -v "{{ $releases_path }}"
    fi

    if [ ! -d "{{ $shared_path }}" ]; then
        mkdir -v "{{ $shared_path }}"
    fi

    if [ ! -d "{{ $backups_path }}" ]; then
        mkdir -v "{{ $backups_path }}"
    fi
@endtask

@error
    if ($task === 'assert:commit') {
        throw new Exception("No tree-ish specified to deploy. Please provide one using '--commit=tree-ish'.");
    } elseif ($task === 'deploy:check') {
        throw new Exception("Unmet prerequisites to deploy. Have you run 'setup' ?");
    } else {
        throw new Exception('Whoops, looks like something went wrong with task: ' . $task);
    }
@enderror