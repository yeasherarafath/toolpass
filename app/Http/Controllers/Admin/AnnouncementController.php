<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Announcement\CreateAnnouncementAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,warning,success'],
            'visible_to' => ['required', 'string', 'in:all,customers,staff,admins'],
        ]);

        app(CreateAnnouncementAction::class)->handle(Auth::user(), $validated);

        return back()->with('success', 'Announcement created.');
    }
}
