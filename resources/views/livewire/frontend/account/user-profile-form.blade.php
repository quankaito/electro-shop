<form wire:submit.prevent="saveProfile">
    <div class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Họ và Tên</label>
            <input
                type="text"
                wire:model.defer="name"
                id="name"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror"
            >
            @error('name')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email (Không thể thay đổi)</label>
            <input
                type="email"
                wire:model.defer="email"
                id="email"
                disabled
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm"
            >
        </div>

        <div>
            <label for="phone_number" class="block text-sm font-medium text-gray-700">Số Điện Thoại</label>
            <input
                type="text"
                wire:model.defer="phone_number"
                id="phone_number"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('phone_number') border-red-500 @enderror"
            >
            @error('phone_number')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="avatar" class="block text-sm font-medium text-gray-700">Ảnh Đại Diện</label>
            <div class="mt-1 flex items-center space-x-4">
                @if ($existingAvatar)
                    <img
                        src="{{ cloudinary_url($existingAvatar) }}"
                        alt="Current Avatar"
                        class="w-16 h-16 rounded-full object-cover"
                    >
                @elseif ($avatar)
                    <img
                        src="{{ $avatar->temporaryUrl() }}"
                        alt="New Avatar Preview"
                        class="w-16 h-16 rounded-full object-cover"
                    >
                @else
                    <span class="inline-block h-16 w-16 rounded-full overflow-hidden bg-gray-100">
                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                @endif

                <input
                    type="file"
                    wire:model="avatar"
                    id="avatar"
                    class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                >
                <div wire:loading wire:target="avatar" class="text-xs text-gray-500">Đang tải lên...</div>
            </div>
            @error('avatar')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="mt-8">
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="saveProfile"
            class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Lưu Thay Đổi
        </button>
    </div>
</form>
