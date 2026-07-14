<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\AdminTask\CompleteAdminTaskAction;
use App\Models\AdminTask;
use Illuminate\Http\Request;

class AdminTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminTask::with(['user', 'userToolAccess.tool', 'otpRequest', 'deviceResetRequest'])
            ->whereIn('status', ['open', 'in_progress'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $tasks = $query->paginate(20);

        return view('admin.tasks.index', compact('tasks'));
    }

    public function show(AdminTask $task)
    {
        $task->load(['user', 'userToolAccess.tool', 'otpRequest', 'deviceResetRequest']);

        return view('admin.tasks.show', compact('task'));
    }

    public function complete(AdminTask $task)
    {
        app(CompleteAdminTaskAction::class)->handle($task, Auth()->id());

        return back()->with('success', 'Task completed.');
    }
}
