<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Lấy cuộc hội thoại và tất cả tin nhắn của user đang đăng nhập.
     * Nếu chưa có, tự động tạo mới.
     */
    public function getConversation()
    {
        $user = Auth::user();

        // firstOrCreate: Tìm cuộc hội thoại của user này, nếu không có thì tạo mới
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id]
        );

        // Lấy tất cả tin nhắn và thông tin người gửi
        $messages = $conversation->messages()->with('user')->oldest()->get();

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ]);
    }

    /**
     * User hoặc Admin gửi một tin nhắn mới.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string',
        ]);

        $user = Auth::user();
        $conversation = Conversation::find($request->conversation_id);

        // =======================================================================
        // ===== BẮT ĐẦU SỬA LỖI: THÊM ĐIỀU KIỆN KIỂM TRA QUYỀN ADMIN =====
        // =======================================================================
        //
        // Kiểm tra quyền: Từ chối nếu user KHÔNG PHẢI là admin VÀ cũng KHÔNG PHẢI là chủ cuộc hội thoại.
        //
        if (!$user->is_admin && $conversation->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden - You do not own this conversation.'], 403);
        }
        // =======================================================================
        // ======================== KẾT THÚC SỬA LỖI ==========================
        // =======================================================================


        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        // Cập nhật thời gian tin nhắn cuối
        $conversation->update(['last_message_at' => now()]);

        // Phát sự kiện đi cho người nhận nghe
        broadcast(new MessageSent($message->load('user')))->toOthers();

        return response()->json($message);
    }
}