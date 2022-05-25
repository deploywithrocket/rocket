cd {!! $release_path !!}

if [ -f "package.json" ]; then
    if [ -f "yarn.lock" ]; then
        echo "📦  Running Yarn…";
        {!! $cmd_yarn !!} config set ignore-engines true
        {!! $cmd_yarn !!} install --pure-lockfile --no-progress --non-interactive
    else
        echo "📦  Running npm…";
        {!! $cmd_npm !!} ci --no-progress --no-audit || {!! $cmd_npm !!} install --no-progress --no-audit
    fi
fi
