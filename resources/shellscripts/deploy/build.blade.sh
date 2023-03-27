cd {!! $release_path !!}

if [ -f "package.json" ]; then
    echo "🌅  Generating assets…";

    if [ -f "yarn.lock" ]; then
        {!! $cmd_yarn !!} run production
    else
        {!! $cmd_npm !!} run production
    fi
fi
