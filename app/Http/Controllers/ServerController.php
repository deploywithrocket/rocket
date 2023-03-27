<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use phpseclib3\Crypt\Common\Formats\Keys\OpenSSH;
use phpseclib3\Crypt\RSA;
use Spatie\Ssh\Ssh as SSH;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::query()
            ->latest()
            ->paginate(15);

        $data = $servers->getCollection();
        $data->each(fn ($item) => $item->setHidden([]));
        $servers->setCollection($data);

        return Inertia::render('Servers/Index', [
            'servers' => $servers,
        ]);
    }

    public function create()
    {
        return Inertia::modal('Servers/Create')
            ->baseRoute('servers.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sshUser' => ['required', 'string', 'max:255'],
            'sshHost' => ['required', 'string', 'max:255'],
            'sshPort' => ['required', 'integer'],
        ]);

        $server = new Server();
        $server->user_id = auth()->id();
        $server->name = $request->name;
        $server->ssh_user = $request->sshUser;
        $server->ssh_host = $request->sshHost;
        $server->ssh_port = $request->sshPort;
        $server->status = 'disconnected';
        $server->save();

        return redirect()->route('servers.connection', $server);
    }

    public function edit(Server $server)
    {
        $server->setHidden([]);

        return Inertia::modal('Servers/Edit', [
            'server' => $server,
        ])->baseRoute('servers.index');
    }

    public function update(Request $request, Server $server)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sshUser' => ['required', 'string', 'max:255'],
            'sshHost' => ['required', 'string', 'max:255'],
            'sshPort' => ['required', 'integer'],
        ]);

        $server->name = $request->name;
        $server->ssh_user = $request->sshUser;
        $server->ssh_host = $request->sshHost;
        $server->ssh_port = $request->sshPort;
        $server->cmd_git = $request->cmdGit;
        $server->cmd_npm = $request->cmdNpm;
        $server->cmd_yarn = $request->cmdYarn;
        $server->cmd_php = $request->cmdPhp;
        $server->cmd_composer = $request->cmdComposer;
        $server->cmd_composer_options = $request->cmdComposerOptions;
        $server->save();

        return redirect()
            ->route('servers.index')
            ->with('success', 'Server updated');
    }

    public function destroy(Server $server)
    {
        rescue(function () use ($server) {
            Storage::delete('keys/' . $server->id);
            Storage::delete('keys/' . $server->id . '.pub');
        });

        $server->delete();

        return redirect()
            ->route('servers.index')
            ->with('success', 'Server deleted');
    }

    public function connection(Server $server)
    {
        $server->setHidden([]);

        if (! Storage::exists('keys/' . $server->id . '.pub')) {
            // Generate unique key for this server
            $comment = Str::slug(config('app.name') . '-' . config('app.env'));
            OpenSSH::setComment($comment);

            $private = RSA::createKey();
            $public = $private->getPublicKey();

            Storage::put('keys/' . $server->id, $private);
            Storage::put('keys/' . $server->id . '.pub', $public->toString('OpenSSH'));

            File::chmod(Storage::path('keys/' . $server->id), 0600);
            File::chmod(Storage::path('keys/' . $server->id . '.pub'), 0600);
        }

        $publicKey = Storage::get('keys/' . $server->id . '.pub');

        return Inertia::modal('Servers/Connection', [
            'server' => $server,
            'publicKey' => $publicKey,
        ])->baseRoute('servers.index');
    }

    public function connectionTest(Server $server)
    {
        $connectedAs = '';

        $connected = rescue(function () use ($server, &$connectedAs) {
            $ssh = SSH::create(
                $server->ssh_user,
                $server->ssh_host,
                $server->ssh_port,
            )
            ->usePrivateKey(Storage::path('keys/' . $server->id))
            ->disableStrictHostKeyChecking();

            $process = $ssh->execute('whoami');
            $connectedAs = trim($process->getOutput());

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            return true;
        });

        $server->status = $connected ? 'connected' : 'disconnected';
        $server->save();

        return redirect()
            ->route('servers.index')
            ->with(
                $connected ? 'success' : 'error',
                $connected
                    ? 'Rocket successfully logged in as ' . $connectedAs . '. Ready for take off!'
                    : 'Rocket was unable to connect to your server.'
            );
    }
}
