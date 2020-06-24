<?php

namespace App\Jobs;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class Test extends Job
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function handle()
    {
        $res = shell_exec("/usr/bin/php7.4 /home/vagrant/code/vendor/bin/dep -f '/home/vagrant/code/deploy/project1/deploy.php' config:dump -o git_tty=false");
        Log::channel('deploy')->info ($res);
    }
}
