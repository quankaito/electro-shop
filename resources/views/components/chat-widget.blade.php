<div x-data='chatWidget({
        conversationId: {{ $conversation->id }},
        initialMessages: @json($conversation->messages()->with("user")->oldest()->get())
    })'
    class="fixed bottom-4 right-4 z-50">

    {{-- Cửa sổ chat --}}
    <div x-show="isOpen" x-transition class="w-80 h-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl flex flex-col" style="display: none;">
        {{-- Header --}}
        <div class="bg-blue-600 text-white p-3 rounded-t-lg flex justify-between items-center">
            <h3 class="font-semibold">Hỗ trợ trực tuyến</h3>
            <button @click="isOpen = false" class="text-white hover:text-gray-200">×</button>
        </div>

        {{-- Khung tin nhắn --}}
        <div x-ref="messagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3">
            <template x-for="message in messages" :key="message.id">
                <div>
                    <template x-if="message.user_id == {{ auth()->id() }}">
                        <div class="flex justify-end">
                            <div class="bg-blue-500 text-white p-2 rounded-lg max-w-[85%] shadow">
                                <p class="text-sm" x-text="message.body"></p>
                                <p class="text-xs mt-1 opacity-75 text-right" x-text="new Date(message.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="message.user_id != {{ auth()->id() }}">
                        <div class="flex justify-start">
                            <div class="bg-gray-200 dark:bg-gray-700 text-white p-2 rounded-lg max-w-[85%] shadow">
                                <p class="text-sm" x-text="message.body"></p>
                                <p class="text-xs mt-1 opacity-75" x-text="new Date(message.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Form nhập tin nhắn --}}
        <div class="p-3 border-t dark:border-gray-700">
            <form @submit.prevent="sendMessage" class="flex items-center">
                <input type="text" x-model="newMessage" @keydown.enter.prevent="sendMessage" placeholder="Nhập tin nhắn..." class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md focus:ring-blue-500 focus:border-blue-500 text-white text-sm">
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shrink-0">Gửi</button>
            </form>
        </div>
    </div>

    {{-- Nút bong bóng chat --}}
    <button @click="isOpen = !isOpen; init()" class="w-16 h-16 bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-blue-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.837 8.837 0 01-4.423-1.211l-3.482.977a.5.5 0 01-.58-.58l.977-3.482A8.837 8.837 0 012 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM4.662 14.534a6.85 6.85 0 004.053 1.256.75.75 0 00.335-1.42a5.353 5.353 0 01-3.12-1.226.75.75 0 00-1.268.99z" clip-rule="evenodd" /></svg>
    </button>
</div>

<script>
    function chatWidget(config) { // Nhận config từ PHP
        return {
            isOpen: false,
            messages: config.initialMessages,
            conversationId: config.conversationId,
            newMessage: '',
            initialized: false,

            init() {
                if (this.initialized) return;
                this.$nextTick(() => this.scrollToBottom());
                this.setupEchoListener();
                this.initialized = true;
            },

            async sendMessage() {
                if (this.newMessage.trim() === '') return;
                const tempMessageBody = this.newMessage;
                this.newMessage = '';
                // 1. Cập nhật tức thời (Optimistic Update)
                this.messages.push({
                    id: Date.now(), body: tempMessageBody, user_id: {{ auth()->id() }},
                    created_at: new Date().toISOString(),
                });
                this.$nextTick(() => this.scrollToBottom());
                try {
                    // 2. Gửi tin nhắn thực tế lên server
                    await axios.post('/api/chat/message', {
                        conversation_id: this.conversationId, body: tempMessageBody,
                    });
                } catch (error) { console.error("Lỗi khi gửi tin nhắn:", error); }
            },

            setupEchoListener() {
                if (window.Echo && this.conversationId) {
                    window.Echo.private(`chat.${this.conversationId}`)
                        .listen('.new-message', (e) => {
                            // ========================================================
                            // =========== BẮT ĐẦU SỬA LỖI TIN NHẮN TRÙNG LẶP ==========
                            // ========================================================
                            //
                            // KIỂM TRA: Nếu ID người gửi tin nhắn từ server
                            // TRÙNG với ID của người dùng hiện tại, thì BỎ QUA.
                            // Vì tin nhắn này đã được hiển thị bằng "Cập nhật tức thời".
                            //
                            if (e.message.user_id == {{ auth()->id() }}) {
                                return;
                            }
                            //
                            // Nếu không trùng (đây là tin nhắn từ Admin), thì hiển thị.
                            //
                            this.messages.push(e.message);
                            this.$nextTick(() => this.scrollToBottom());
                            // ========================================================
                            // ============ KẾT THÚC SỬA LỖI TIN NHẮN TRÙNG LẶP =========
                            // ========================================================
                        });
                }
            },

            scrollToBottom() { this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight; }
        }
    }
</script>