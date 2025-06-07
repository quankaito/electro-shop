<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Khung hiển thị các tin nhắn --}}
        <div wire:poll.10s class="h-[60vh] overflow-y-auto space-y-4 p-4 border dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
            @forelse($this->record->messages()->with('user')->oldest()->get() as $message)
                
                {{-- SỬA LỖI SO SÁNH: Dùng "==" thay vì "===" --}}
                @if($message->user_id == auth()->id())
                    {{-- Tin nhắn của Admin (bên phải) --}}
                    <div class="flex justify-end">
                        <div class="bg-primary-500 text-white p-3 rounded-lg max-w-[85%] shadow">
                            <p class="text-sm">{{ $message->body }}</p>
                            <p class="text-xs text-primary-200 mt-1 text-right">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @else
                    {{-- Tin nhắn của User (bên trái) --}}
                    <div class="flex justify-start">
                        <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg max-w-[85%] shadow">
                            <p class="font-semibold text-xs mb-1">{{ $message->user->name ?? 'User' }}</p>
                            <p class="text-sm">{{ $message->body }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-center text-gray-500">Chưa có tin nhắn nào.</p>
            @endforelse
        </div>

        {{-- Form gửi tin nhắn --}}
        <form wire:submit.prevent="sendMessage">
            <div class="flex items-center">
                <x-filament::input.wrapper class="w-full">
                    <x-filament::input
                        type="text"
                        wire:model="body"
                        placeholder="Nhập tin nhắn..."
                    />
                </x-filament::input.wrapper>

                <x-filament::button type="submit" class="ml-2">
                    Gửi
                </x-filament::button>
            </div>
            @error('body') <span class="text-sm text-danger-500">{{ $message }}</span> @enderror
        </form>
    </div>
</x-filament-panels::page>