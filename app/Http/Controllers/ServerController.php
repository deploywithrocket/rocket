<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::query()
            ->with('projects')
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
            'user' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $server = new Server();
        $server->name = $request->name;
        $server->user = $request->user;
        $server->address = $request->address;
        $server->save();

        return redirect()->route('servers.show', $server->id);
    }

    public function show(Server $server)
    {
        $server = $server->load('directory');

        return inertia('servers/show', compact('campaign'));
    }

    public function edit(Server $server)
    {
        return inertia('server/edit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $server->name = $request->name;
        $server->user = $request->user;
        $server->address = $request->address;
        $server->save();

        return redirect()->route('server.show', $server->id);
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('server.index');
    }
}
