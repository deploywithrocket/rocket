<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib\Crypt\RSA;
use Spatie\Ssh\Ssh as SSH;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::query()
            ->latest()
            ->paginate(10);

        return inertia('servers/index', [
            'servers' => $servers,
        ]);
    }

    public function create()
    {
        return inertia('servers/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ssh_user' => ['required', 'string', 'max:255'],
            'ssh_host' => ['required', 'string', 'max:255'],
            'ssh_port' => ['required', 'integer'],
        ]);

        $server = new Server();
        $server->user_id = auth()->id();
        $server->name = $request->name;
        $server->ssh_user = $request->ssh_user;
        $server->ssh_host = $request->ssh_host;
        $server->ssh_port = $request->ssh_port;
        $server->status = 'disconnected';
        $server->save();

        return redirect()->route('servers.connection', $server->id);
    }

    public function connection(Server $server)
    {
        $ip_addresses = config('app.ip_addresses');

        try {
            $public_key = Storage::get('keys/' . $server->id . '.pub');
        } catch (\Throwable $th) {
            // Generate unique key for this server
            $comment = Str::slug(config('app.name') . '-' . config('app.env'));

            $rsa = new RSA();
            $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
            $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
            $rsa->setComment($comment);
            $key = $rsa->createKey(2048);

            Storage::put('keys/' . $server->id, $key['privatekey']);
            Storage::put('keys/' . $server->id . '.pub', $key['publickey']);

            File::chmod(Storage::path('keys/' . $server->id), 0600);
            File::chmod(Storage::path('keys/' . $server->id . '.pub'), 0600);

            $public_key = $key['publickey'];
        }

        return inertia('servers/connection', compact('server', 'public_key', 'ip_addresses'));
    }

    public function connectionTest(Server $server)
    {
        $connected_as = '';

        $connected = rescue(function () use ($server, &$connected_as) {
            $this->ssh = SSH::create(
                $server->ssh_user,
                $server->ssh_host,
                $server->ssh_port,
            )
            ->usePrivateKey(Storage::path('keys/' . $server->id))
            ->disableStrictHostKeyChecking();

            $process = $this->ssh->execute('whoami');
            $connected_as = trim($process->getOutput());

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            return true;
        });

        $server->status = $connected ? 'connected' : 'disconnected';
        $server->save();

        return redirect()
            ->route('servers.connection', $server)
            ->with(
                $connected ? 'success' : 'error',
                $connected
                    ? 'Rocket successfully logged in as ' . $connected_as . '. Ready for take off!'
                    : 'Rocket was unable to connect to your server.'
            );
    }

    public function edit(Server $server)
    {
        return inertia('servers/edit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ssh_user' => ['required', 'string', 'max:255'],
            'ssh_host' => ['required', 'string', 'max:255'],
            'ssh_port' => ['required', 'integer'],
        ]);

        $server->name = $request->name;
        $server->ssh_user = $request->ssh_user;
        $server->ssh_host = $request->ssh_host;
        $server->ssh_port = $request->ssh_port;
        $server->cmd_git = $request->cmd_git;
        $server->cmd_npm = $request->cmd_npm;
        $server->cmd_yarn = $request->cmd_yarn;
        $server->cmd_php = $request->cmd_php;
        $server->cmd_composer = $request->cmd_composer;
        $server->cmd_composer_options = $request->cmd_composer_options;
        $server->save();

        return redirect()
            ->route('servers.index')
            ->with('success', 'Server updated');
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()
            ->route('servers.index')
            ->with('success', 'Server deleted');
    }
}
