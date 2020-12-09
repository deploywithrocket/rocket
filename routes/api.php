<?php

use App\Jobs\EnvoyDeployJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api', 'as' => 'api.'], function () {
});

Route::get('/servers/{server}/connect', function (Server $server) {
    $key = Storage::get('keys/' . $server->id . '.pub');

    echo '#!/bin/bash' . PHP_EOL
        . "echo \"🔑  Adding $server->name key for user \$(whoami)\"…" . PHP_EOL
        . "echo \"$key\" >> ~/.ssh/authorized_keys" . PHP_EOL
        . 'echo "Key added!"';

    exit;
})->name('api.servers.connect');

Route::post('/projects/{project}/deploy', function (Request $request, Project $project) {
    $token = $project->tokens()->firstWhere('token', $request->token);

    if (! $token) {
        return response()->json(['error' => 'Token not found.'], 500);
    }

    $token->fill(['last_used_at' => now()])->save();

    if (! $project->push_to_deploy) {
        return response()->json(['error' => 'PTD is not enabled on this project.'], 500);
    }

    if ($request->header('x-github-event') == 'push') {
        // Something have been pushed!
        [, $foo, $bar] = explode('/', $request->ref);

        if ($foo == 'heads') {
            // That's a commit on a $bar branch
            if ($bar == $project->branch) {
                // Trigger deployment

                [$user, $repo] = explode('/', $project->repository);
                $gh_client = $project->user->github()->repository();

                $gh_branch = rescue(fn () => $gh_client->branches($user, $repo, $project->branch));

                if (! $gh_branch) {
                    return response()->json(['error', 'It seems that we are unable to access the configured repository.'], 500);
                }

                $commit = [
                    'sha' => $gh_branch['commit']['sha'],
                    'message' => $gh_branch['commit']['commit']['message'],
                    'committer' => [
                        'login' => $gh_branch['commit']['committer']['login'],
                        'avatar_url' => $gh_branch['commit']['committer']['avatar_url'],
                    ],
                    'repo' => $project->repository,
                    'from_ref' => 'heads/' . $gh_branch['name'],
                ];

                $deployment = $project->deployments()->create([
                    'server_id' => $project->server->id,
                    'status' => 'queued',
                    'release' => date('YmdHis'),
                    'commit' => $commit,
                ]);

                dispatch(new EnvoyDeployJob($deployment));

                return response()->json(['message' => 'Deploying release ' . $deployment->release], 200);
            }
        }

        if ($foo == 'tags') {
            // That's the $bar tag. We ignore that for the moment.
            // TODO
        }
    }

    return response()->json(['message' => 'Nothing to do!'], 200);
})->name('api.projects.deploy');

Route::get('/projects/{project}/shield', function (Request $request, Project $project) {
    $deployment = $project->latest_deployment;

    $color = Str::of($deployment->status)
        ->replace('success', 'ec4899')
        ->replace('queued', 'blue')
        ->replace('in_progress', 'blue')
        ->replace('error', 'red')
        ->replace('abandoned', 'inactive')
        ->__toString();

    return response()->json([
        'schemaVersion' => 1,
        'label' => 'rocket',
        'message' => $deployment->created_at->diffForHumans(),
        'color' => $color,
        'logoSvg' => File::get(public_path('images/brand_flatwhite.svg')),
    ]);
})->name('api.projects.shield');
