<?php

namespace App\Listeners;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProjectEventSubscriber
{
    public function handleCreated ($event) {
        $operation = new Operation ();

        if (is_object( $event->user)) {
            $operation->user_id = $event->user->id;
        }

        $operation->project_id = $event->project->id;
        $operation->action = 'create';
        $operation->is_successfully = 1;
        $operation->message = 'OK';
        $operation->save ();
    }

    public function handleDeploySuccess ($event) {
        $operation = new Operation ();

        if (is_object( $event->user)) {
            $operation->user_id = $event->user->id;
        }

        $operation->project_id = $event->project->id;
        $operation->action = 'deploy';
        $operation->is_successfully = 1;
        $operation->message = 'OK';
        $operation->save ();

        $event->project->deployed_at = Carbon::now();
        $event->project->save ();

        if (get_system_setting('sys_use_logs', 0) == 1) {
            Log::channel('deploy')->info('Deploy success');
        }
    }

    public function handleDeployError ($event) {
        $operation = new Operation ();

        if (is_object( $event->user)) {
            $operation->user_id = $event->user->id;
        }

        $operation->project_id = $event->project->id;
        $operation->action = 'deploy';
        $operation->is_successfully = 0;
        $operation->message = $event->message;
        $operation->save ();

        if (get_system_setting('sys_use_logs', 0) == 1) {
            Log::channel('deploy')->error('Deploy error: ' . $event->message);
        }
    }

    public function handleRollbackSuccess ($event) {
        $operation = new Operation ();

        if (is_object( $event->user)) {
            $operation->user_id = $event->user->id;
        }

        $operation->project_id = $event->project->id;
        $operation->action = 'rollback';
        $operation->is_successfully = 1;
        $operation->message = 'OK';
        $operation->save ();

        if (get_system_setting('sys_use_logs', 0) == 1) {
            Log::channel('deploy')->info('Rollback success');
        }
    }

    public function handleRollbackError ($event) {
        $operation = new Operation ();

        if (is_object( $event->user)) {
            $operation->user_id = $event->user->id;
        }

        $operation->project_id = $event->project->id;
        $operation->action = 'rollback';
        $operation->is_successfully = 0;
        $operation->message = $event->message;
        $operation->save ();

        if (get_system_setting('sys_use_logs', 0) == 1) {
            Log::channel('deploy')->error('Rollback error: ' . $event->message);
        }
    }

    public function subscribe($events)
    {
        $events->listen (
            'App\Events\ProjectCreated',
            'App\Listeners\ProjectEventSubscriber@handleCreated'
        );

        $events->listen (
            'App\Events\ProjectDeployed',
            'App\Listeners\ProjectEventSubscriber@handleDeploySuccess'
        );

        $events->listen (
            'App\Events\ProjectDeployFailed',
            'App\Listeners\ProjectEventSubscriber@handleDeployError'
        );

        $events->listen (
            'App\Events\ProjectRollbacked',
            'App\Listeners\ProjectEventSubscriber@handleRollbackSuccess'
        );

        $events->listen (
            'App\Events\ProjectRollbackFailed',
            'App\Listeners\ProjectEventSubscriber@handleRollbackError'
        );
    }
}
