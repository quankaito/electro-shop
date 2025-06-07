<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Tìm cuộc hội thoại
    $conversation = \App\Models\Conversation::find($conversationId);

    // Nếu không tìm thấy, từ chối
    if (!$conversation) {
        return false;
    }

    // Cho phép nếu user là admin, hoặc user là chủ của cuộc hội thoại này
    return $user->is_admin || $user->id === $conversation->user_id;
});