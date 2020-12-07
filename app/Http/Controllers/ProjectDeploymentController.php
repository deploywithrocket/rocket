<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Project;

class ProjectDeploymentController extends Controller
{
    public function show(Project $project, Deployment $deployment)
    {
        abort_if($deployment->project_id != $project->id, 404);

        return inertia('projects/deployments/show', compact('project', 'deployment'));
    }
}
