<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->with('latest_deployment')
            ->paginate(20);

        $data = $projects->getCollection();
        $data->each(fn ($item) => $item->setHidden([]));
        $projects->setCollection($data);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $servers = Server::query()
            ->where('status', 'connected')
            ->pluck('name', 'id');

        return Inertia::modal('Projects/Create')
            ->with(['servers' => $servers])
            ->baseRoute('projects.index');
    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'repository' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'serverId' => ['required', 'exists:servers,id'],
            'environment' => ['required', 'string', 'max:255'],
            'deployPath' => ['required', 'string', 'max:255'],
        ]);

        $validator->validate();
        $this->validateRepo($request->repository, $request->branch, $validator);

        $server = Server::find($request->serverId);
        if (! $server) {
            $validator->errors()->add('serverId', 'Server not found.');
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
        $project->live_url = $request->liveUrl;
        $project->branch = $request->branch;
        $project->server_id = $request->serverId;
        $project->environment = $request->environment;
        $project->deploy_path = $request->deployPath;

        // Laravel preset
        if ($request->useLaravelPreset) {
            $project->cron_jobs = '* * * * * ' . ($server->cmd_php ?? 'php') . ' ' . $project->deploy_path . '/current/artisan schedule:run >> ' . $project->deploy_path . '/shared/storage/logs/scheduler.log';
            $project->linked_dirs = ['storage/app', 'storage/framework', 'storage/logs'];
            $project->hooks = [
                'built' => ''
                    . 'cd {!! $release_path !!}' . PHP_EOL
                    . PHP_EOL
                    . 'echo "⭐  Laravel magic!"' . PHP_EOL
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

            // Try to load .env.example
            [$user, $repo] = explode('/', $project->repository);
            $ghClient = auth()->user()->github()->repository()->contents();
            $project->env = rescue(fn () => base64_decode($ghClient->show($user, $repo, '.env.example')['content']), null, false);
        }

        $project->save();

        return redirect()->route('projects.show', $project->id);
    }

    public function show(Project $project)
    {
        $deploymentsStats = [
            'today' => $project->deployments()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'thisWeek' => $project->deployments()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'latestDuration' => $project->deployments()->latest()->first()?->duration,
        ];

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'deploymentsStats' => $deploymentsStats,
        ]);
    }

    public function edit(Project $project)
    {
        return redirect()->route('projects.edit.common', $project);
    }

    public function editCommon(Project $project)
    {
        $project->makeVisible(['server_id', 'environment', 'deploy_path']);

        $servers = Server::query()
            ->where('status', 'connected')
            ->pluck('name', 'id');

        return Inertia::render('Projects/Edit/Common', [
            'project' => $project,
            'servers' => $servers,
        ]);
    }

    public function updateCommon(Request $request, Project $project)
    {
        $validator = validator()->make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'repository' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'liveUrl' => ['string', 'max:255'],
            'serverId' => ['required', 'exists:servers,id'],
            'environment' => ['required', 'string', 'max:255'],
            'deployPath' => ['required', 'string', 'max:255'],
        ]);

        $validator->validate();
        $this->validateRepo($request->repository, $request->branch, $validator);

        $server = Server::find($request->serverId);
        if (! $server) {
            $validator->errors()->add('serverId', 'Server not found.');
        }

        if (count($validator->errors()->messages())) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->input());
        }

        $project->name = $request->name;
        $project->repository = $request->repository;
        $project->live_url = $request->liveUrl;
        $project->server_id = $request->serverId;
        $project->environment = $request->environment;
        $project->deploy_path = $request->deployPath;

        $project->save();

        return redirect()
            ->route('projects.edit.common', $project)
            ->with('success', 'Project updated!');
    }

    public function editEnvFile(Project $project)
    {
        $project->makeVisible(['env']);

        return Inertia::render('Projects/Edit/EnvFile', [
            'project' => $project,
        ]);
    }

    public function updateEnvFile(Request $request, Project $project)
    {
        $project->env = $request->env;
        $project->save();

        return redirect()
            ->route('projects.edit.env-file', $project)
            ->with('success', 'Environment file updated!');
    }

    public function editHooks(Project $project)
    {
        $project->makeVisible(['hooks']);

        return Inertia::render('Projects/Edit/Hooks', [
            'project' => $project,
        ]);
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
            ->route('projects.edit.hooks', $project)
            ->with('success', 'Hooks updated!');
    }

    public function editCronJobs(Project $project)
    {
        $project->makeVisible(['cron_jobs']);

        return Inertia::render('Projects/Edit/CronJobs', [
            'project' => $project,
        ]);
    }

    public function updateCronJobs(Request $request, Project $project)
    {
        $project->cron_jobs = $request->cronJobs;
        $project->save();

        return redirect()
            ->route('projects.edit.cron-jobs', $project)
            ->with('success', 'Cron jobs updated!');
    }

    public function editShared(Project $project)
    {
        $project->makeVisible(['linked_dirs', 'linked_files']);

        return inertia('Projects/Edit/Shared', [
            'project' => $project,
        ]);
    }

    public function updateShared(Request $request, Project $project)
    {
        $project->linked_dirs = $request->linkedDirs;
        $project->linked_files = $request->linkedFiles;
        $project->save();

        return redirect()
            ->route('projects.edit.shared', $project)
            ->with('success', 'Linked directories updated!');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }

    protected function validateRepo($repository, $branch, &$validator)
    {
        // Can we access the project?
        [$user, $repo] = explode('/', $repository);
        $ghClient = auth()->user()->github()->repository();

        if (! rescue(fn () => $ghClient->show($user, $repo), null, false)) {
            $validator->errors()->add('repository', "Whoops! It seems that we can't access this repository.");
        }

        if (! rescue(fn () => $ghClient->branches($user, $repo, $branch), null, false)) {
            $validator->errors()->add('branch', 'Whoops! Unknown branch.');
        }
    }
}
