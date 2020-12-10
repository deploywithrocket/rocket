@foreach ($linked_dirs as $from => $to)
    echo '🔗  Linking directory {!! $from !!} to {!! $to !!}…'

    mkdir -p `dirname {!! $to !!}`

    if [ -d {!! $from !!} ]; then
        if [ ! -d {!! $to !!} ]; then
            cp -r {!! $from !!} {!! $to !!}
        fi

        rm -rf {!! $from !!}
    fi

    if [ ! -d {!! $to !!} ]; then
        mkdir {!! $to !!}
    fi

    mkdir -p `dirname {!! $from !!}`

    ln -srfn {!! $to !!} {!! $from !!}
@endforeach

@foreach ($linked_files as $from => $to)
    echo "🔗  Linking file {!! $from !!} to {!! $to !!}…"

    mkdir -p `dirname {!! $to !!}`

    if [ -f {!! $from !!} ]; then
        if [ ! -f {!! $to !!} ]; then
            cp {!! $from !!} {!! $to !!}
        fi

        rm -f {!! $from !!}
    fi

    if [ ! -f {!! $to !!} ]; then
        touch {!! $to !!}
    fi

    mkdir -p `dirname {!! $from !!}`

    ln -srfn {!! $to !!} {!! $from !!}
@endforeach
