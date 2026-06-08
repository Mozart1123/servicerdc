<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessageController extends Controller
{
    // ── Helpers ────────────────────────────────────────────────────────────────

    private function oneCol(): string
    {
        return \Schema::hasColumn('conversations', 'user_one_id') ? 'user_one_id' : 'user_one';
    }

    private function twoCol(): string
    {
        return \Schema::hasColumn('conversations', 'user_two_id') ? 'user_two_id' : 'user_two';
    }

    // ── Actions ────────────────────────────────────────────────────────────────

    /**
     * Display all conversations + optional active conversation.
     */
    public function index(Request $request): View
    {
        $user   = Auth::user();
        $oneCol = $this->oneCol();
        $twoCol = $this->twoCol();

        $conversations = Conversation::where($oneCol, $user->id)
            ->orWhere($twoCol, $user->id)
            ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->sortByDesc(fn($c) => $c->messages->first()?->created_at ?? $c->created_at);

        $unreadCount = 0;
        foreach ($conversations as $conv) {
            $unreadCount += $conv->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
        }

        $activeConversation = null;
        $messages           = collect();

        if ($request->has('id')) {
            $activeConversation = Conversation::with(['userOne', 'userTwo'])->findOrFail($request->id);

            if (!$activeConversation->hasParticipant($user->id)) {
                abort(403);
            }

            $messages = $activeConversation->messages()->with('sender')->oldest()->get();

            // Mark messages as read
            $activeConversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('user.messages.index', compact(
            'conversations',
            'activeConversation',
            'messages',
            'unreadCount'
        ));
    }

    /**
     * Send a message in an existing conversation.
     */
    public function send(Request $request, Conversation $conversation): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'content'    => 'required_without:attachment|string|max:2000|nullable',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,webp,pdf,doc,docx',
        ]);

        $user = Auth::user();

        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $oneCol = $this->oneCol();
        $twoCol = $this->twoCol();

        $receiverId = ($conversation->getAttribute($oneCol) ?? $conversation->user_one) == $user->id
            ? ($conversation->getAttribute($twoCol) ?? $conversation->user_two)
            : ($conversation->getAttribute($oneCol) ?? $conversation->user_one);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages/attachments', 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'receiver_id'     => $receiverId,
            'content'         => $request->input('content'),
            'message'         => $request->input('content'),
            'attachment'      => $attachmentPath,
            'is_read'         => false,
        ]);

        // Notification
        Notification::create([
            'user_id' => $receiverId,
            'type'    => 'message',
            'title'   => 'Nouveau message de ' . $user->name,
            'message' => \Str::limit($request->input('content') ?? 'Fichier joint', 80),
            'data'    => [
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
            ],
            'is_read' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message->load('sender')]);
        }

        return redirect()->route('user.messages.index', ['id' => $conversation->id]);
    }

    /**
     * Legacy store route (uses conversation_id from body).
     */
    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content'         => 'required|string|max:2000',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        return $this->send($request, $conversation);
    }

    /**
     * Start or open a conversation with another user, then redirect.
     */
    public function startConversation(Request $request, User $user): RedirectResponse
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas vous envoyer un message.');
        }

        $relatedType = $request->related_type;
        $relatedId   = $request->related_id;

        $conversation = Conversation::findOrCreateBetween($authUser->id, $user->id, $relatedType, $relatedId);

        return redirect()->route('user.messages.index', ['id' => $conversation->id]);
    }

    /**
     * POST /messages/start — body: {user_id, related_type?, related_id?}
     */
    public function start(Request $request): RedirectResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::findOrFail($request->user_id);
        return $this->startConversation($request, $user);
    }

    /**
     * Mark all messages in conversation as read.
     */
    public function markRead(Conversation $conversation): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
