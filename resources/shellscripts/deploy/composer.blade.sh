cd {!! $release_path !!}

if [ -f "composer.json" ]; then
    echo "🚚  Running Composer…";
    {!! $cmd_composer !!} install {!! $cmd_composer_options !!}
fi
