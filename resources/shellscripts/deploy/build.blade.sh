cd {!! $release_path !!}

if [ -f "package.json" ]; then
    echo "🌅  Generating assets…";

    if [ -f "yarn.lock" ]; then
        {!! $cmd_yarn !!} run production --no-progress
    else
        {!! $cmd_npm !!} run production --no-progress
    fi

    echo "Removing \"node_modules\" directory"
    rm -rf node_modules
fi
