<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::query()
            ->orderBy('created_at', 'DESC')
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
        ]);

        $server = new Server();
        $server->name = $request->name;
        $server->ssh_user = $request->ssh_user;
        $server->ssh_host = $request->ssh_host;
        $server->save();

        return redirect()->route('servers.show', $server->id);
    }

    public function show(Server $server)
    {
        $server = $server->load('projects');

        return inertia('servers/show', compact('server'));
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
        ]);

        $server->name = $request->name;
        $server->ssh_user = $request->ssh_user;
        $server->ssh_host = $request->ssh_host;

        $server->ssh_options = $request->ssh_options;
        $server->cmd_git = $request->cmd_git;
        $server->cmd_npm = $request->cmd_npm;
        $server->cmd_yarn = $request->cmd_yarn;
        $server->cmd_bower = $request->cmd_bower;
        $server->cmd_grunt = $request->cmd_grunt;
        $server->cmd_php = $request->cmd_php;
        $server->cmd_composer = $request->cmd_composer;
        $server->cmd_composer_options = $request->cmd_composer_options;

        $server->save();

        return redirect()->route('servers.show', $server->id);
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('servers.index');
    }
}
