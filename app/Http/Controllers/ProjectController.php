<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Server;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return inertia('projects/index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $servers = Server::pluck('name', 'id');

        return inertia('projects/create', compact('servers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->user = $request->user;
        $project->address = $request->address;
        $project->save();

        return redirect()->route('projects.show', $project->id);
    }

    public function show(Project $project)
    {
        $project = $project->load('directory');

        return inertia('projects/show', compact('campaign'));
    }

    public function edit(Project $project)
    {
        $servers = Server::pluck('name', 'id');

        return inertia('project/edit', compact('project', 'servers'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $project->name = $request->name;
        $project->user = $request->user;
        $project->address = $request->address;
        $project->save();

        return redirect()->route('project.show', $project->id);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('project.index');
    }
}
