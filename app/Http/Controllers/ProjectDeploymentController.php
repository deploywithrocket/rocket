<?php

namespace App\Http\Controllers;

use App\Jobs\DeployJob;
use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectDeploymentController extends Controller
{
    public function create(Project $project)
    {
        return Inertia::modal('Projects/Deployments/Create', [
            'project' => $project,
            'types' => [
                'commit' => 'Commit',
                'branch' => 'Branch',
                'tag' => 'Tag',
            ],
        ])
        ->baseRoute('projects.deployments.index', $project);
    }

    public function store(Request $request, Project $project)
    {
        $types = ['commit', 'branch', 'tag'];

        $request->validate([
            'type' => ['required', Rule::in($types)],
            'target' => ['required'],
        ]);

        [$user, $repo] = explode('/', $project->repository);
        $ghClient = auth()->user()->github();

        if ($request->type == 'branch' || $request->type == 'tag') {
            $ref = ($request->type == 'branch' ? 'heads/' : 'tags/') . $request->target;

            $gh_ref = rescue(fn () => $ghClient->git()->references()->show($user, $repo, $ref));

            if (! $gh_ref) {
                return redirect()
                    ->back()
                    ->with('error', 'Whoops! We are unable to access the configured repository or find the ' . $ref . ' ref.');
            }

            $target_commit = $gh_ref['object']['sha'];
        }

        $ghCommit = rescue(fn () => $ghClient->repository()->commits()->show($user, $repo, $target_commit ?? $request->target));

        if (! $ghCommit) {
            return redirect()
                ->back()
                ->with('error', 'Whoops! We are unable to access the configured repository or find the ' . $request->target . ' commit.');
        }

        $commit = [
            'sha' => $ghCommit['sha'],
            'message' => $ghCommit['commit']['message'],
            'committer' => [
                'login' => $ghCommit['committer']['login'],
                'avatar_url' => $ghCommit['committer']['avatar_url'],
            ],
            'repo' => $project->repository,
            'from_ref' => $ref ?? null,
        ];

        $deployment = $project->deployments()->create([
            'server_id' => $project->server->id,
            'status' => 'queued',
            'release' => date('YmdHis'),
            'commit' => $commit,
        ]);

        dispatch(new DeployJob($deployment));

        return redirect()
            ->route('projects.deployments.index', [$project])
            ->with('success', 'Project queued for deployment');
    }

    public function index(Project $project)
    {
        $deployments = $project
            ->deployments()
            ->latest()
            ->paginate();

        return inertia('Projects/Deployments/Index', compact('project', 'deployments'));
    }

    public function show(Project $project, Deployment $deployment)
    {
        abort_if($deployment->project_id != $project->id, 404);
        $deployment->makeVisible(['raw_output']);

        return inertia('Projects/Deployments/Show', compact('project', 'deployment'));
    }
}
