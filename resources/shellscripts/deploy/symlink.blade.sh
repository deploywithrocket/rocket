echo "🔗  Linking directory {!! $release_path !!} to {!! $current_path !!}…"

ln -srfn {!! $release_path !!} {!! $current_path !!}
