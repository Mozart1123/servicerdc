<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    return $conversation && $conversation->hasParticipant($user->id);
});

Broadcast::channel('presence-chat', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
