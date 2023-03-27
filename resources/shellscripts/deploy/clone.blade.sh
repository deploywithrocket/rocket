echo "🌀  Cloning repository…"

mkdir {!! $release_path !!}
cd {!! $release_path !!}

{!! $cmd_git !!} --git-dir {!! $repository_path !!} --work-tree {!! $release_path !!} checkout -f {!! $ref !!}
{!! $cmd_git !!} --git-dir {!! $repository_path !!} --work-tree {!! $release_path !!} rev-parse HEAD > {!! $revision_path !!}
