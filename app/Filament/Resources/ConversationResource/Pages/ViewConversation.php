<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Events\MessageSent;
use App\Filament\Resources\ConversationResource;
use App\Models\Message;
use Filament\Resources\Pages\ViewRecord;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;
    protected static string $view = 'filament.resources.conversation-resource.pages.view-conversation';

    public string $body = '';

    public function getListeners(): array
    {
        return [
            "echo-private:chat.{$this->record->id},new-message" => 'handleNewMessage',
        ];
    }

    public function handleNewMessage($payload): void
    {
        // Khi Echo đã được tải đúng, chỉ cần dòng này là đủ để Livewire
        // tải lại dữ liệu từ database và cập nhật giao diện.
        $this->record->refresh();
    }

    public function sendMessage(): void
    {
        $this->validate(['body' => 'required|string']);

        $message = Message::create([
            'conversation_id' => $this->record->id,
            'user_id'         => auth()->id(),
            'body'            => $this->body,
        ]);

        $this->record->update(['last_message_at' => now()]);

        // Gửi tin nhắn đến người nhận
        broadcast(new MessageSent($message->load('user')))->toOthers();

        // Tự động thêm tin nhắn vừa gửi vào giao diện mà không cần F5
        $this->record->refresh();
        $this->reset('body');
    }
}