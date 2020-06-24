<?php

namespace App\Events;
use App\Models\User;
use App\Models\Project;

class ProjectDeployed extends Event
{
    public $project;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Project $project, User $user)
    {
        $this->project = $project;
        $this->user = $user;
    }
}
