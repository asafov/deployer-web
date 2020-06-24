<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Auth;
use App\Jobs\Deploy;
use App\Jobs\Rollback;
use App\Jobs\Status;

class ProjectController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }

    /*
     * Get projects list
     */
    public function index () {
        $projectsData = Auth::user()->projects()->where('is_active', 1)->get();

        $projects = [];
        foreach ($projectsData as $project) {
            $projects[] = [
                'name' => $project->name,
                'slug' => $project->slug,
                'allow_deploy' => $project->permissions->allow_deploy,
                'allow_rollback' => $project->permissions->allow_rollback,
            ];
        }

        return response()->json([
            'result' => true,
            'error' => '',
            'projects' => $projects,
        ]);
    }

    /*
     * Get project information
     */
    public function get ($project) {
        $projectData = \App\Models\Project::where ('slug', $project)
            ->where ('is_active', 1)
            ->first ();

        if (!is_object($projectData)) {
            return response()->json([
                'result' => false,
                'error' => 'Incorrect project',
            ]);
        }

        $projectAccess = Auth::user()->projects()
            ->where('is_active', 1)
            ->where('user_projects.project_id', $projectData->id);

        if (!$projectAccess->exists ()) {
            return response()->json([
                'result' => false,
                'error' => 'Access denied to project',
            ]);
        }

        $accessData = $projectAccess->first ();
        $accessData->permissions->allow_deploy;
        $accessData->permissions->allow_rollback;

    }

    /*
     * Get project status
     */
    public function status ($project) {
        $projectData = \App\Models\Project::where ('slug', $project)
            ->where ('is_active', 1)
            ->first ();

        if (!is_object($projectData)) {
            return response()->json([
                'result' => false,
                'error' => 'Incorrect project',
            ]);
        }

        $projectAccess = Auth::user()->projects()
            ->where('is_active', 1)
            ->where('user_projects.project_id', $projectData->id);

        if (!$projectAccess->exists ()) {
            return response()->json([
                'result' => false,
                'error' => 'Access denied to project',
            ]);
        }


    }

    /*
     * Deploy project
     */
    public function deploy ($project) {
        $projectData = \App\Models\Project::where ('slug', $project)
            ->where ('is_active', 1)
            ->first ();

        if (!is_object($projectData)) {
            return response()->json([
                'result' => false,
                'error' => 'Incorrect project',
            ]);
        }

        $projectAccess = Auth::user()->projects()
            ->where('is_active', 1)
            ->where('user_projects.project_id', $projectData->id);

        if (!$projectAccess->exists ()) {
            return response()->json([
                'result' => false,
                'error' => 'Access denied to project',
            ]);
        }

        $accessData = $projectAccess->first ();
        if ($accessData->permissions->allow_deploy == 0) {
            return response()->json([
                'result' => false,
                'error' => 'Access denied to deploy project',
            ]);
        }

        dispatch (new Deploy ($projectData, Auth::user()));

        return response()->json([
            'result' => true,
        ]);
    }

    /*
     * Rollback project
     */
    public function rollback ($project) {
        $projectData = \App\Models\Project::where ('slug', $project)
            ->where ('is_active', 1)
            ->first ();

        if (!is_object($projectData)) {
            return response()->json([
                'result' => false,
                'error' => 'Incorrect project',
            ]);
        }

        $projectAccess = Auth::user()->projects()
            ->where('is_active', 1)
            ->where('user_projects.project_id', $projectData->id);

        if (!$projectAccess->exists ()) {
            return response()->json([
                'result' => false,
                'error' => 'Access denied to project',
            ]);
        }

        $accessData = $projectAccess->first ();
        if ($accessData->permissions->allow_rollback == 0) {
            return response()->json([
                'result' => false,
                'error' => 'Access denied to rollback project',
            ]);
        }

        dispatch (new Rollback ($projectData, Auth::user()));

        return response()->json([
            'result' => true,
        ]);
    }
}
