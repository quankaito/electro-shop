<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Quan trọng
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast // Implement ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // Tên kênh mà tin nhắn sẽ được gửi đến
    public function broadcastOn(): array
    {
        // PrivateChannel đảm bảo chỉ người trong cuộc hội thoại mới nghe được
        return [
            new PrivateChannel('chat.' . $this->message->conversation_id),
        ];
    }

    // Tên của sự kiện khi gửi đi (để JS dễ bắt)
    public function broadcastAs()
    {
        return 'new-message';
    }
}