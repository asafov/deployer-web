<?php

namespace App\Jobs;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Project;
use App\Events\ProjectDeployed;
use App\Events\ProjectDeployFailed;

class Deploy extends Job
{
    protected $project;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project, $user = NULL)
    {
        $this->project = $project;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $deployConfigFile = base_path('deploy/' . basename ($this->project->slug) . '/deploy.php');

        if (!file_exists($deployConfigFile)) {
            event(new ProjectDeployFailed($this->project, $this->user), 'Deploy configuration file not exists');
            return false;
        }

        $deployerTask = 'deploy';

        $taskData = [
            phpPath (),
            depPath (),
            '-f ' . $deployConfigFile,
            $deployerTask,
        ];

        if (!Process::isTtySupported()) {
            // Update option in config file for tty
            $taskData[] = '-o git_tty=false';
        }

        if (get_system_setting('dep_quiet_mode', 0) == 1) {
            //$taskData[] = '-q';
        }

        if (get_system_setting('dep_ignore_interaction', 0) == 1) {
            $taskData[] = '-n';
        }

        if (get_system_setting('dep_use_logs', 0) == 1) {
            $taskData[] = '--log=' . storage_path('logs/deployer_' . basename ($this->project->slug) . '.log');
        }

        $shellString = implode (' ', $taskData);

        $process = Process::fromShellCommandline ($shellString);
        $process->setTty(Process::isTtySupported());

        try {
            $process->mustRun();
            echo $process->getOutput();
        } catch (ProcessFailedException $exception) {
            event(new ProjectDeployFailed($this->project, $this->user), $exception->getMessage());
            return false;
        }
        echo $process->getOutput();
        event(new ProjectDeployed($this->project, $this->user));
    }
}
