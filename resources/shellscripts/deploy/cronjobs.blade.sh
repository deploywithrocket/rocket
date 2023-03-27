@if ($cron_jobs)

echo "⏲  Setting up cron jobs…"

FILE=$(mktemp)
crontab -l > $FILE || true

sed -i '/# ROCKET BEGIN {!! $fingerprint !!}/,/# ROCKET END {!! $fingerprint !!}/d' $FILE

cat <<EOF-ROCKET >> $FILE
# ROCKET BEGIN {!! $fingerprint !!}
SHELL="/bin/bash"
{!! $cron_jobs !!}
# ROCKET END {!! $fingerprint !!}
EOF-ROCKET

if [ -s $FILE ]; then
    crontab $FILE
else
    crontab -r || true
fi

rm $FILE

@endif
