<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Actions\Support\CreateTicketAction;
use App\Actions\Support\ReplyTicketAction;
use App\Models\SupportTicket;
use App\Models\UserToolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->latest()->paginate(10);

        return view('customer.support.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load('messages.sender');

        return view('customer.support.show', compact('ticket'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
            'user_tool_access_id' => ['nullable', 'exists:user_tool_accesses,id'],
        ]);

        $access = null;
        if (! empty($validated['user_tool_access_id'])) {
            $access = UserToolAccess::where('user_id', Auth::id())
                ->findOrFail($validated['user_tool_access_id']);
        }

        $ticket = app(CreateTicketAction::class)->handle(Auth::user(), [
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'user_tool_access_id' => $access?->id,
            'tool_id' => $access?->tool_id,
            'tool_account_id' => $access?->tool_account_id,
        ]);

        return redirect()->route('customer.support.show', $ticket)
            ->with('success', 'Ticket created.');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        app(ReplyTicketAction::class)->handle($ticket, Auth::user(), $validated['message'], false);

        return back()->with('success', 'Reply sent.');
    }
}
