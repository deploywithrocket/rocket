echo "🗑️  Cleaning up old releases…"

cd {!! $releases_path !!}

for RELEASE in $(ls -1d * | head -n -{!! $keep_releases !!}); do
    echo "Deleting old release $RELEASE"
    rm -rf "$RELEASE"
done

echo "🚀  Application deployed!"