<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function tickets()
    {
        $tickets = \App\Models\SupportTicket::with('user')->latest()->paginate(20);
        return view('admin.support.tickets', compact('tickets'));
    }

    public function replyTicket(Request $request, $id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);
        $request->validate(['reply' => 'required|string']);

        $ticket->update([
            'admin_reply' => $request->reply,
            'status' => 'pending',
            'replied_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function closeTicket($id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);
        $ticket->update(['status' => 'closed']);
        return response()->json(['success' => true]);
    }

    public function docs()
    {
        return view('admin.support.docs');
    }

    public function suggestions()
    {
        $suggestions = \App\Models\Suggestion::with('user')->latest()->paginate(20);
        return view('admin.support.suggestions', compact('suggestions'));
    }

    public function toggleSuggestionStatus($id)
    {
        $suggestion = \App\Models\Suggestion::findOrFail($id);
        $nextStatus = $suggestion->status == 'pending' ? 'reviewed' : ($suggestion->status == 'reviewed' ? 'implemented' : 'pending');
        $suggestion->update(['status' => $nextStatus]);
        return response()->json(['success' => true, 'status' => $nextStatus]);
    }
}
