<?php

namespace App\Actions\AdminTask;

use App\Actions\Access\DeliverAccessAction;
use App\Models\AdminTask;

class CompleteAdminTaskAction
{
    public function handle(AdminTask $task, ?int $completedBy = null): AdminTask
    {
        if ($task->status === 'completed') {
            return $task;
        }

        $task->status = 'completed';
        $task->completed_at = now();
        $task->save();

        if ($task->type === 'invite_user' && $task->user_tool_access_id) {
            $access = $task->userToolAccess;
            if ($access && $access->delivery_status !== 'delivered') {
                app(DeliverAccessAction::class)->handle($access);
            }
        }

        return $task;
    }
}
