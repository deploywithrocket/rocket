export GIT_SSH_COMMAND="ssh -q -o PasswordAuthentication=no -o VerifyHostKeyDNS=yes"

{!! $cmd_git !!} --git-dir {!! $repository_path !!} remote set-url origin {!! $repository_url !!}
{!! $cmd_git !!} --git-dir {!! $repository_path !!} fetch origin +refs/heads/*:refs/heads/* +refs/tags/*:refs/tags/* --prune
