<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Display all user conversations for admin oversight.
     */
    public function index(Request $request): View
    {
        $query = Conversation::with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('userOne', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('userTwo', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $conversations = $query->paginate(20)->appends($request->query());

        $stats = [
            'total' => Conversation::count(),
            'today' => Conversation::whereDate('created_at', today())->count(),
            'messages' => Message::count(),
        ];

        return view('admin.messages.index', compact('conversations', 'stats'));
    }

    /**
     * View a specific conversation's messages.
     */
    public function show(Conversation $conversation): View
    {
        $conversation->load(['userOne', 'userTwo']);
        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return view('admin.messages.show', compact('conversation', 'messages'));
    }
}
