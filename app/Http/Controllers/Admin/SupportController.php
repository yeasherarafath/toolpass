<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Support\ReplyTicketAction;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $tickets = $query->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('messages.sender', 'user');

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        app(ReplyTicketAction::class)->handle($ticket, Auth::user(), $validated['message'], true);

        return back()->with('success', 'Reply sent.');
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->status = 'closed';
        $ticket->closed_at = now();
        $ticket->save();

        return back()->with('success', 'Ticket closed.');
    }
}
