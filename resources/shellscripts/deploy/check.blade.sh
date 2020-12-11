echo "🏃  Deploying {!! $ref !!} as release {!! $release !!} to host {!! $ssh_host !!}…"

if [ ! -d {!! $repository_path !!} ]; then
    echo "Repository path not found." 1>&2
    exit 1
fi

if [ "$(git --git-dir {!! $repository_path !!} rev-parse --is-bare-repository)" != "true" ]; then
    echo "Repository is not bare." 1>&2
    exit 1
fi

if [ ! -d {!! $releases_path !!} ]; then
    echo "Releases path not found." 1>&2
    exit 1
fi

if [ ! -d {!! $shared_path !!} ]; then
    echo "Shared path not found." 1>&2
    exit 1
fi

echo "✅  All checks passed!"
