<?php

namespace App\Events;
use App\Models\User;
use App\Models\Project;

class ProjectDeployFailed extends Event
{
    public $project;
    public $user;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Project $project, User $user, $message)
    {
        $this->project = $project;
        $this->user = $user;
        $this->message = $message;
    }
}
