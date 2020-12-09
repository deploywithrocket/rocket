<?php

namespace App\Http\Controllers;

use App\Http\Utils\Discord;
use App\Jobs\EnvoyDeployJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->with('latest_deployment')
            ->get()
            ->sortByDesc('latest_deployment.created_at');

        return inertia('projects/index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $servers = Server::query()
            ->where('status', 'connected')
            ->pluck('name', 'id');

        return inertia('projects/create', compact('servers'));
    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'repository' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'server_id' => ['required', 'exists:servers,id'],
            'environment' => ['required', 'string', 'max:255'],
            'deploy_path' => ['required', 'string', 'max:255'],
        ]);

        $validator->validate();
        $this->validateRepo($request->repository, $request->branch, $validator);

        $server = Server::find($request->server_id);
        if (! $server) {
            $validator->errors()->add('server_id', 'Server not found.');
        }

        if (count($validator->errors()->messages())) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->input());
        }

        $project = new Project();
        $project->user_id = auth()->id();
        $project->name = $request->name;
        $project->repository = $request->repository;
        $project->live_url = $request->live_url;
        $project->branch = $request->branch;
        $project->server_id = $request->server_id;
        $project->environment = $request->environment;
        $project->deploy_path = $request->deploy_path;

        // Laravel preset
        $project->cron_jobs = '* * * * * ' . ($server->cmd_php ?? 'php') . ' ' . $project->deploy_path . '/current/artisan schedule:run >> ' . $project->deploy_path . '/shared/storage/logs/scheduler.log';
        $project->linked_dirs = ['storage/app', 'storage/framework', 'storage/logs'];
        $project->hooks = [
            'built' => ''
                . 'cd [[release]]' . PHP_EOL
                . PHP_EOL
                . 'php artisan down' . PHP_EOL
                . PHP_EOL
                . 'php artisan storage:link' . PHP_EOL
                . PHP_EOL
                . 'php artisan config:clear' . PHP_EOL
                . 'php artisan view:clear' . PHP_EOL
                . 'php artisan cache:clear' . PHP_EOL
                . 'php artisan clear-compiled' . PHP_EOL
                . 'php artisan config:cache' . PHP_EOL
                . PHP_EOL
                . 'php artisan migrate --force' . PHP_EOL
                . PHP_EOL
                . 'php artisan up' . PHP_EOL,
        ];

        $project->save();

        return redirect()->route('projects.show', $project->id);
    }

    public function show(Project $project)
    {
        $project = $project
            ->load('server');

        $deployments = $project->deployments()->orderBy('created_at', 'DESC')->limit(10)->get();

        $deployments_stats = [
            'today' => $project->deployments()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'this_week' => $project->deployments()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return inertia('projects/show', compact('project', 'deployments', 'deployments_stats'));
    }

    public function edit(Project $project)
    {
        $servers = Server::query()
            ->where('status', 'connected')
            ->pluck('name', 'id');

        return inertia('projects/edit', compact('project', 'servers'));
    }

    public function update(Request $request, Project $project)
    {
        $validator = validator()->make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'repository' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'live_url' => ['string', 'max:255'],
            'server_id' => ['required', 'exists:servers,id'],
            'environment' => ['required', 'string', 'max:255'],
            'deploy_path' => ['required', 'string', 'max:255'],
        ]);

        $validator->validate();
        $this->validateRepo($request->repository, $request->branch, $validator);

        $server = Server::find($request->server_id);
        if (! $server) {
            $validator->errors()->add('server_id', 'Server not found.');
        }

        if (count($validator->errors()->messages())) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->input());
        }

        $project->name = $request->name;
        $project->repository = $request->repository;
        $project->live_url = $request->live_url;
        $project->server_id = $request->server_id;
        $project->environment = $request->environment;
        $project->deploy_path = $request->deploy_path;
        $project->discord_webhook_url = $request->discord_webhook_url;
        $project->push_to_deploy = $request->push_to_deploy;
        $project->save();

        if ($request->push_to_deploy) {
            $this->hookPTD($project);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated!');
    }

    public function editEnvFile(Project $project)
    {
        return inertia('projects/edit/env-file', compact('project'));
    }

    public function updateEnvFile(Request $request, Project $project)
    {
        $project->env = $request->env;
        $project->save();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Environment file updated!');
    }

    public function editHooks(Project $project)
    {
        return inertia('projects/edit/hooks', compact('project'));
    }

    public function updateHooks(Request $request, Project $project)
    {
        $project->hooks = [
            'started' => $request->started,
            'provisioned' => $request->provisioned,
            'built' => $request->built,
            'published' => $request->published,
            'finished' => $request->finished,
        ];
        $project->save();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Hooks updated!');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }

    public function deploy(Project $project)
    {
        // Get latest commit
        [$user, $repo] = explode('/', $project->repository);
        $gh_client = auth()->user()->github()->repository();
        $gh_branch = rescue(fn () => $gh_client->branches($user, $repo, $project->branch));

        if (! $gh_branch) {
            return redirect()
                ->back()
                ->with('error', 'Whoops! It seems that we are unable to access the configured repository.');
        }

        $commit = array_merge(
            [
                'from_branch' => $gh_branch['name'],
                'from_repository' => $project->repository,
            ],
            $gh_branch['commit'],
        );

        $deployment = $project->deployments()->create([
            'server_id' => $project->server->id,
            'status' => 'queued',
            'release' => date('YmdHis'),
            'commit' => $commit,
        ]);

        dispatch(new EnvoyDeployJob($deployment));

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project queued for deployment');
    }

    protected function validateRepo($repository, $branch, &$validator)
    {
        // Can we access the project?
        [$user, $repo] = explode('/', $repository);
        $gh_client = auth()->user()->github()->repository();

        if (! rescue(fn () => $gh_client->show($user, $repo))) {
            $validator->errors()->add('repository', "Whoops! It seems that we can't access this repository.");
        }

        if (! rescue(fn () => $gh_client->branches($user, $repo, $branch))) {
            $validator->errors()->add('branch', 'Whoops! Unknown branch.');
        }
    }

    public function testDiscordWebhook(Project $project)
    {
        try {
            (new Discord($project->discord_webhook_url))
                ->webhook('Webhook is working 😀🔥!');
        } catch (\Throwable $th) {
            return redirect()
            ->route('projects.show', $project)
            ->with('success', 'An error occurred while sending the message');
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'A test message has been sent !');
    }

    public function hookPTD(Project $project)
    {
        [$user, $repo] = explode('/', $project->repository);
        $gh_client = auth()->user()->github()->repository()->hooks();

        // Hook will not register if it already exists
        $wh_url = route('api.projects.deploy', $project);
        rescue(fn () => $gh_client->create($user, $repo, [
            'name' => 'web',
            'events' => ['push'],
            'config' => ['url' => $wh_url, 'content_type' => 'json'],
        ]));

        return true;
    }
}
