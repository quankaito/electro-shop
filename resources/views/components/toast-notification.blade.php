<!-- resources/views/components/toast-notification.blade.php -->
<div
    x-data="{ show: false, message: '', type: 'info' }"
    x-on:show-toast.window="message = $event.detail.message; type = $event.detail.type || 'info'; show = true; setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition
    x-cloak
    class="fixed bottom-5 right-5 p-4 rounded-md shadow-lg text-white"
    :class="{ 'bg-green-500': type === 'success', 'bg-blue-500': type === 'info', 'bg-red-500': type === 'error', 'bg-yellow-500': type === 'warning' }"
>
    <p x-text="message"></p>
</div>