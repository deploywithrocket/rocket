<?php $task = isset($task) ? $task : null; ?>
<?php $repo_path = isset($repo_path) ? $repo_path : null; ?>
<?php $RELEASE = isset($RELEASE) ? $RELEASE : null; ?>
<?php $keep_releases = isset($keep_releases) ? $keep_releases : null; ?>
<?php $cron_job = isset($cron_job) ? $cron_job : null; ?>
<?php $cron_jobs = isset($cron_jobs) ? $cron_jobs : null; ?>
<?php $fingerprint = isset($fingerprint) ? $fingerprint : null; ?>
<?php $FILE = isset($FILE) ? $FILE : null; ?>
<?php $releasePath = isset($releasePath) ? $releasePath : null; ?>
<?php $cmd_npm = isset($cmd_npm) ? $cmd_npm : null; ?>
<?php $cmd_yarn = isset($cmd_yarn) ? $cmd_yarn : null; ?>
<?php $cmd_composer_options = isset($cmd_composer_options) ? $cmd_composer_options : null; ?>
<?php $cmd_composer = isset($cmd_composer) ? $cmd_composer : null; ?>
<?php $path = isset($path) ? $path : null; ?>
<?php $assets_path = isset($assets_path) ? $assets_path : null; ?>
<?php $copied_files = isset($copied_files) ? $copied_files : null; ?>
<?php $current_path = isset($current_path) ? $current_path : null; ?>
<?php $copied_dirs = isset($copied_dirs) ? $copied_dirs : null; ?>
<?php $file = isset($file) ? $file : null; ?>
<?php $linked_files = isset($linked_files) ? $linked_files : null; ?>
<?php $dir = isset($dir) ? $dir : null; ?>
<?php $linked_dirs = isset($linked_dirs) ? $linked_dirs : null; ?>
<?php $release_path = isset($release_path) ? $release_path : null; ?>
<?php $repository_url = isset($repository_url) ? $repository_url : null; ?>
<?php $cmd_git = isset($cmd_git) ? $cmd_git : null; ?>
<?php $shared_path = isset($shared_path) ? $shared_path : null; ?>
<?php $releases_path = isset($releases_path) ? $releases_path : null; ?>
<?php $repository_path = isset($repository_path) ? $repository_path : null; ?>
<?php $backups_path = isset($backups_path) ? $backups_path : null; ?>
<?php $ssh_host = isset($ssh_host) ? $ssh_host : null; ?>
<?php $release = isset($release) ? $release : null; ?>
<?php $commit = isset($commit) ? $commit : null; ?>
<?php $server_string = isset($server_string) ? $server_string : null; ?>
<?php $deptrace = isset($deptrace) ? $deptrace : null; ?>
<?php
    extract(
        json_decode(
            file_get_contents(
                $deptrace
            ),
            true
        )
    );

    var_dump($deptrace);
    exit;
?>

<?php $__container->servers(['web' => $server_string]); ?>

<?php $__container->startTask('assert:commit'); ?>
    <?php if (! $commit): ?>
        echo "Commit not defined." 1>&2
        exit 1
    <?php else: ?>
        echo "Deploying release <?php echo $release; ?> with tree-ish <?php echo $commit; ?> to <?php echo $ssh_host; ?>..."
    <?php endif; ?>
<?php $__container->endTask(); ?>

<?php $__container->startMacro('backups'); ?>
    backups:list
<?php $__container->endMacro(); ?>

<?php $__container->startTask('backups:list'); ?>
    ls -1 "<?php echo $backups_path; ?>"
<?php $__container->endTask(); ?>

<?php $__container->startMacro('deploy'); ?>
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
<?php $__container->endMacro(); ?>

<?php $__container->startTask('deploy:starting'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:check'); ?>
    if [ ! -d "<?php echo $repository_path; ?>" ]; then
        echo "Repository path not found." 1>&2
        exit 1
    fi

    if [ "$(git --git-dir <?php echo $repository_path; ?> rev-parse --is-bare-repository)" != "true" ]; then
        echo "Repository is not bare." 1>&2
        exit 1
    fi

    if [ ! -d "<?php echo $releases_path; ?>" ]; then
        echo "Releases path not found." 1>&2
        exit 1
    fi

    if [ ! -d "<?php echo $shared_path; ?>" ]; then
        echo "Shared path not found." 1>&2
        exit 1
    fi

    if [ ! -d "<?php echo $backups_path; ?>" ]; then
        echo "Backups path not found." 1>&2
        exit 1
    fi

    echo "All checks passed!"
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:backup'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:started'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:provisioning'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:fetch'); ?>
    export GIT_SSH_COMMAND="ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes"

    <?php echo $cmd_git; ?> --git-dir "<?php echo $repository_path; ?>" remote set-url origin "<?php echo $repository_url; ?>"
    <?php echo $cmd_git; ?> --git-dir "<?php echo $repository_path; ?>" fetch origin +refs/heads/*:refs/heads/* +refs/tags/*:refs/tags/* --prune
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:release'); ?>
    mkdir "<?php echo $release_path; ?>"
    cd "<?php echo $release_path; ?>"

    <?php echo $cmd_git; ?> --git-dir "<?php echo $repository_path; ?>" --work-tree "<?php echo $release_path; ?>" checkout -f <?php echo $commit; ?>

    <?php echo $cmd_git; ?> --git-dir "<?php echo $repository_path; ?>" --work-tree "<?php echo $release_path; ?>" rev-parse HEAD > "<?php echo $release_path; ?>/REVISION"
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:link'); ?>
    @run('deploy:link:dirs')
    @run('deploy:link:files')
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:link:dirs'); ?>
    <?php foreach ($linked_dirs as $dir): ?>
        echo "Linking directory <?php echo $release_path; ?>/<?php echo $dir; ?> to <?php echo $shared_path; ?>/<?php echo $dir; ?>"

        mkdir -p `dirname "<?php echo $shared_path; ?>/<?php echo $dir; ?>"`

        if [ -d "<?php echo $release_path; ?>/<?php echo $dir; ?>" ]; then
            if [ ! -d "<?php echo $shared_path; ?>/<?php echo $dir; ?>" ]; then
                cp -r "<?php echo $release_path; ?>/<?php echo $dir; ?>" "<?php echo $shared_path; ?>/<?php echo $dir; ?>"
            fi

            rm -rf "<?php echo $release_path; ?>/<?php echo $dir; ?>"
        fi

        if [ ! -d "<?php echo $shared_path; ?>/<?php echo $dir; ?>" ]; then
            mkdir "<?php echo $shared_path; ?>/<?php echo $dir; ?>"
        fi

        mkdir -p `dirname "<?php echo $release_path; ?>/<?php echo $dir; ?>"`

        ln -srfn "<?php echo $shared_path; ?>/<?php echo $dir; ?>" "<?php echo $release_path; ?>/<?php echo $dir; ?>"
    <?php endforeach; ?>
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:link:files'); ?>
    <?php foreach ($linked_files as $file): ?>
        echo "Linking file <?php echo $release_path; ?>/<?php echo $file; ?> to <?php echo $shared_path; ?>/<?php echo $file; ?>"

        mkdir -p `dirname "<?php echo $shared_path; ?>/<?php echo $file; ?>"`

        if [ -f "<?php echo $release_path; ?>/<?php echo $file; ?>" ]; then
            if [ ! -f "<?php echo $shared_path; ?>/<?php echo $file; ?>" ]; then
                cp "<?php echo $release_path; ?>/<?php echo $file; ?>" "<?php echo $shared_path; ?>/<?php echo $file; ?>"
            fi

            rm -f "<?php echo $release_path; ?>/<?php echo $file; ?>"
        fi

        if [ ! -f "<?php echo $shared_path; ?>/<?php echo $file; ?>" ]; then
            touch "<?php echo $shared_path; ?>/<?php echo $file; ?>"
        fi

        mkdir -p `dirname "<?php echo $release_path; ?>/<?php echo $file; ?>"`

        ln -srfn "<?php echo $shared_path; ?>/<?php echo $file; ?>" "<?php echo $release_path; ?>/<?php echo $file; ?>"
    <?php endforeach; ?>
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:copy'); ?>
    @run('deploy:copy:dirs')
    @run('deploy:copy:files')
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:copy:dirs'); ?>
    <?php foreach ($copied_dirs as $dir): ?>
        echo "Copying directory <?php echo $current_path; ?>/<?php echo $dir; ?> to <?php echo $release_path; ?>/<?php echo $dir; ?>"

        mkdir -p `dirname "<?php echo $release_path; ?>/<?php echo $dir; ?>"`

        if [ -d "<?php echo $current_path; ?>/<?php echo $dir; ?>" ]; then
            rsync -a "<?php echo $current_path; ?>/<?php echo $dir ? rtrim($dir, '/') .'/' : ''; ?>" "<?php echo $release_path; ?>/<?php echo $dir; ?>"
        fi
    <?php endforeach; ?>
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:copy:files'); ?>
    <?php foreach ($copied_files as $file): ?>
        echo "Copying file <?php echo $current_path; ?>/<?php echo $file; ?> to <?php echo $release_path; ?>/<?php echo $file; ?>"

        mkdir -p `dirname "<?php echo $release_path; ?>/<?php echo $file; ?>"`

        if [ -f "<?php echo $current_path; ?>/<?php echo $file; ?>" ]; then
            rsync -a "<?php echo $current_path; ?>/<?php echo $file; ?>" "<?php echo $release_path; ?>/<?php echo $file; ?>"
        fi
    <?php endforeach; ?>
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:composer'); ?>
    <?php foreach (array_unique([$release_path, $assets_path]) as $path): ?>
        cd "<?php echo $path; ?>"

        if [ -f "composer.json" ]; then
            <?php echo $cmd_composer; ?> install <?php echo $cmd_composer_options; ?> --prefer-dist --optimize-autoloader --no-progress --no-interaction
        fi
    <?php endforeach; ?>
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:npm'); ?>
    cd "<?php echo $assets_path; ?>"

    if [ -f "package.json" ]; then
        if [ -f "yarn.lock" ]; then
            <?php echo $cmd_yarn; ?> install --pure-lockfile --no-progress --non-interactive
        else
            <?php echo $cmd_npm; ?> install
        fi
    fi
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:provisioned'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:building'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:build'); ?>
    cd "<?php echo $assets_path; ?>"

    if [ -f "package.json" ]; then
        if [ -f "yarn.lock" ]; then
            <?php echo $cmd_yarn; ?> run production --no-progress
        else
            <?php echo $cmd_npm; ?> run production --no-progress
        fi
    fi
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:built'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:publishing'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:symlink'); ?>
    echo "Linking directory <?php echo $release_path; ?> to <?php echo $current_path; ?>"

    ln -srfn "<?php echo $release_path; ?>" "<?php echo $current_path; ?>"
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:publish'); ?>
    cd "<?php echo $releasePath; ?>"

    php artisan down
    php artisan migrate --force

    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link

    php artisan up
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:cronjobs'); ?>
    FILE=$(mktemp)
    crontab -l > $FILE || true

    sed -i '/# ROCKET BEGIN <?php echo $fingerprint; ?>/,/# ROCKET END <?php echo $fingerprint; ?>/d' $FILE

    <?php if (is_array($cron_jobs) && count($cron_jobs) > 0): ?>
        echo '# ROCKET BEGIN <?php echo $fingerprint; ?>' >> $FILE
        echo 'SHELL="/bin/bash"' >> $FILE
        <?php foreach ($cron_jobs as $cron_job): ?>
            echo <?php echo escapeshellarg($cron_job); ?> >> $FILE
        <?php endforeach; ?>
        echo '# ROCKET END <?php echo $fingerprint; ?>' >> $FILE
    <?php endif; ?>

    if [ -s $FILE ]; then
        crontab $FILE
    else
        crontab -r || true
    fi

    rm $FILE
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:published'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:finishing'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:cleanup'); ?>
    cd "<?php echo $releases_path; ?>"

    for RELEASE in $(ls -1d * | head -n -<?php echo $keep_releases; ?>); do
        echo "Deleting old release $RELEASE"
        rm -rf "$RELEASE"
    done
<?php $__container->endTask(); ?>

<?php $__container->startTask('deploy:finished'); ?>
    true
<?php $__container->endTask(); ?>

<?php $__container->startMacro('releases'); ?>
    releases:list
<?php $__container->endMacro(); ?>

<?php $__container->startTask('releases:list'); ?>
    ls -1 "<?php echo $releases_path; ?>"
<?php $__container->endTask(); ?>

<?php $__container->startMacro('rollback'); ?>
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
<?php $__container->endMacro(); ?>

<?php $__container->startTask('rollback:symlink'); ?>
    <?php if (isset($release)): ?>
        RELEASE="<?php echo $release; ?>"
    <?php else: ?>
        cd "<?php echo $releases_path; ?>"
        RELEASE=`ls -1d */ | head -n -1 | tail -n 1 | sed "s/\/$//"`
    <?php endif; ?>

    if [ ! -d "<?php echo $releases_path; ?>/$RELEASE" ]; then
        echo "Release $RELEASE not found. Could not rollback."
        exit 1
    fi

    echo "Linking directory <?php echo $releases_path; ?>/$RELEASE to <?php echo $current_path; ?>"

    ln -srfn "<?php echo $releases_path; ?>/$RELEASE" "<?php echo $current_path; ?>"
<?php $__container->endTask(); ?>

<?php $__container->startMacro('setup'); ?>
    setup:repository
    setup:directories
<?php $__container->endMacro(); ?>

<?php $__container->startTask('setup:repository'); ?>
    export GIT_SSH_COMMAND="ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes"

    if [ -d "<?php echo $repo_path; ?>" ]; then
        echo "Deleting directory <?php echo $repo_path; ?>" 1>&2
        rm -rf "<?php echo $repo_path; ?>"
    fi

    if [ ! -d "<?php echo $repository_path; ?>" ]; then
        <?php echo $cmd_git; ?> clone --bare "<?php echo $repository_url; ?>" "<?php echo $repository_path; ?>"
        <?php echo $cmd_git; ?> --git-dir "<?php echo $repository_path; ?>" config advice.detachedHead false
    fi
<?php $__container->endTask(); ?>

<?php $__container->startTask('setup:directories'); ?>
    if [ ! -d "<?php echo $releases_path; ?>" ]; then
        mkdir -v "<?php echo $releases_path; ?>"
    fi

    if [ ! -d "<?php echo $shared_path; ?>" ]; then
        mkdir -v "<?php echo $shared_path; ?>"
    fi

    if [ ! -d "<?php echo $backups_path; ?>" ]; then
        mkdir -v "<?php echo $backups_path; ?>"
    fi
<?php $__container->endTask(); ?>

<?php $_vars = get_defined_vars(); $__container->error(function($task) use ($_vars) { extract($_vars, EXTR_SKIP); 
    if ($task === 'assert:commit') {
        throw new Exception("No tree-ish specified to deploy. Please provide one using '--commit=tree-ish'.");
    } elseif ($task === 'deploy:check') {
        throw new Exception("Unmet prerequisites to deploy. Have you run 'setup' ?");
    } else {
        throw new Exception('Whoops, looks like something went wrong with task ' . $task);
    }
}); ?>