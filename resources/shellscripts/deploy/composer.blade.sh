cd {!! $release_path !!}

if [ -f "composer.json" ]; then
    echo "🚚  Running Composer…";
    {!! $cmd_composer !!} install --no-interaction --no-progress --no-suggest {!! $cmd_composer_options !!}
fi
