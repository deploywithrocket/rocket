@if ($env_contents)

echo "🖨️  Writing environment file…"
cat >{!! $env_path !!} <<EOF-ROCKET
{!! $env_contents !!}
EOF-ROCKET

@endif
