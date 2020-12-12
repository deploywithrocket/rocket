<?php

namespace App\Http\Controllers;

use App\Jobs\DeployJob;
use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectDeploymentController extends Controller
{
    public function create(Project $project)
    {
        return inertia('projects/deployments/create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $types = ['commit', 'branch', 'tag'];

        $request->validate([
            'type' => ['required', Rule::in($types)],
            'target' => ['required'],
        ]);

        [$user, $repo] = explode('/', $project->repository);
        $gh_client = auth()->user()->github();

        if ($request->type == 'branch' || $request->type == 'tag') {
            $ref = ($request->type == 'branch' ? 'heads/' : 'tags/') . $request->target;

            $gh_ref = rescue(fn () => $gh_client->git()->references()->show($user, $repo, $ref));

            if (! $gh_ref) {
                return redirect()
                    ->back()
                    ->with('error', 'Whoops! We are unable to access the configured repository or find the ' . $ref . ' ref.');
            }

            $target_commit = $gh_ref['object']['sha'];
        }

        $gh_commit = rescue(fn () => $gh_client->repository()->commits()->show($user, $repo, $target_commit ?? $request->target));

        if (! $gh_commit) {
            return redirect()
                ->back()
                ->with('error', 'Whoops! We are unable to access the configured repository or find the ' . $request->target . ' commit.');
        }

        $commit = [
            'sha' => $gh_commit['sha'],
            'message' => $gh_commit['commit']['message'],
            'committer' => [
                'login' => $gh_commit['committer']['login'],
                'avatar_url' => $gh_commit['committer']['avatar_url'],
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
            ->route('projects.show', $project)
            ->with('success', 'Project queued for deployment');
    }

    public function index(Project $project)
    {
        $deployments = $project
            ->deployments()
            ->with('ping')
            ->latest()
            ->paginate();

        return inertia('projects/deployments/index', compact('project', 'deployments'));
    }

    public function show(Project $project, Deployment $deployment)
    {
        abort_if($deployment->project_id != $project->id, 404);

        $deployment->load('ping');

        return inertia('projects/deployments/show', compact('project', 'deployment'));
    }
}
