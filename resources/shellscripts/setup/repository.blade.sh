export GIT_SSH_COMMAND="ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes"

if [ ! -d {!! $repository_path !!} ]; then
    {!! $cmd_git !!} clone --bare {!! $repository_url !!} {!! $repository_path !!}
    {!! $cmd_git !!} --git-dir {!! $repository_path !!} config advice.detachedHead false
fi
