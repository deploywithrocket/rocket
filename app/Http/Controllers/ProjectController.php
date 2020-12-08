<?php

namespace App\Http\Controllers;

use App\Jobs\EnvoyDeployJob;
use App\Jobs\EnvoySetupJob;
use App\Models\Project;
use App\Models\Server;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
            'repository' => ['required', 'string', 'max:255'],
            'server_id' => ['required', 'exists:servers,id'],
            'deploy_path' => ['required', 'string', 'max:255'],
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->repository = $request->repository;

        $project->server_id = $request->server_id;
        $project->deploy_path = $request->deploy_path;

        $project->save();

        return redirect()->route('projects.show', $project->id);
    }

    public function show(Project $project)
    {
        $project = $project
            ->load('server');

        $deployments = $project->deployments()->orderBy('created_at', 'DESC')->limit(10)->get();

        return inertia('projects/show', compact('project', 'deployments'));
    }

    public function edit(Project $project)
    {
        $servers = Server::pluck('name', 'id');

        // try {
        //     $github_token = optional(
        //         auth()
        //         ->user()
        //         ->social_accounts()
        //         ->where('provider', 'github')
        //         ->first()
        //     )->token;

        //     Config::set('github.connections.main.token', $github_token);

        //     $repositories = collect(GitHub::connection('main')->me()->repositories())
        //         ->pluck('ssh_url', 'full_name')
        //         ->toArray();
        // } catch (\Throwable $th) {
        // }
        $repositories = null;

        return inertia('projects/edit', compact('project', 'servers', 'repositories'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'repository_url' => ['required', 'string', 'max:255'],
            'health_url' => ['required', 'string', 'max:255'],
            'server_id' => ['required', 'exists:servers,id'],
            'deploy_path' => ['required', 'string', 'max:255'],
        ]);

        $project->name = $request->name;
        $project->repository_url = $request->repository_url;
        $project->health_url = $request->health_url;
        $project->server_id = $request->server_id;
        $project->deploy_path = $request->deploy_path;
        $project->env = $request->env;
        $project->save();

        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }

    public function setup(Project $project)
    {
        $deployment = $project->deployments()->create([
            'server_id' => $project->server->id,
            'type' => 'setup',
            'release' => '',
            'commit' => '',
        ]);

        dispatch(new EnvoySetupJob($deployment));

        return redirect()->route('projects.show', $project);
    }

    public function deploy(Project $project)
    {
        // Get latest commit
        $github_token = optional(
            auth()
            ->user()
            ->social_accounts()
            ->where('provider', 'github')
            ->first()
        )->token;

        Config::set('github.connections.main.token', $github_token);

        [, $user_repo] = explode(':', $project->repository_url);
        [$user, $repo_ext] = explode('/', $user_repo);
        [$repo, ] = explode('.', $repo_ext);

        $github_most_recent_commit = collect(
            GitHub::connection('main')
                ->repository()
                ->commits()
                ->all($user, $repo, [
                    'branch' => 'main',
                ])
        )->first();

        $deployment = $project->deployments()->create([
            'server_id' => $project->server->id,
            'type' => 'deploy',
            'release' => date('YmdHis'),
            'commit' => $github_most_recent_commit['sha'],
        ]);

        dispatch(new EnvoyDeployJob($deployment));

        return redirect()->route('projects.show', $project);
    }
}
