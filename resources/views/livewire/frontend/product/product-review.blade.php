<div class="space-y-8">
    @auth
        @if ($canReview)
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-3">{{ $hasReviewed ? 'Chỉnh Sửa Đánh Giá Của Bạn' : 'Viết Đánh Giá Của Bạn' }}</h3>
            <form wire:submit.prevent="submitReview">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đánh giá của bạn (sao):</label>
                    <div class="flex space-x-1">
                        @foreach(range(1, 5) as $star)
                            <button type="button" wire:click="setRating({{ $star }})"
                                    class="focus:outline-none {{ $rating >= $star ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </button>
                        @endforeach
                    </div>
                    @error('rating') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">Bình luận của bạn (tuỳ chọn):</label>
                    <textarea wire:model.defer="comment" id="comment" rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    @error('comment') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="submitReview"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ $hasReviewed ? 'Cập Nhật Đánh Giá' : 'Gửi Đánh Giá' }}
                </button>
            </form>
        </div>
        @else
        <div class="bg-yellow-50 p-4 rounded-lg text-yellow-700 text-sm">
            Bạn cần mua sản phẩm này để có thể đánh giá.
        </div>
        @endif
    @else
        <div class="bg-blue-50 p-4 rounded-lg text-blue-700 text-sm">
            Vui lòng <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="font-semibold underline">đăng nhập</a> để viết đánh giá.
        </div>
    @endauth

    <h3 class="text-xl font-semibold mt-8 mb-4">Tất Cả Đánh Giá ({{ $reviews->total() }})</h3>
    @if($reviews->isNotEmpty())
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="flex space-x-4 border-b pb-4 last:border-b-0 last:pb-0" wire:key="review-{{ $review->id }}">
                    <img src="{{ $review->user->avatar ? cloudinary_url($review->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($review->user->name).'&color=7F9CF5&background=EBF4FF' }}"
                         alt="{{ $review->user->name }}" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <div class="flex items-center mb-1">
                            <h4 class="font-semibold text-gray-800 mr-2">{{ $review->user->name }}</h4>
                            <div class="flex">
                                @foreach(range(1,5) as $star)
                                    <svg class="w-4 h-4 {{ $review->rating >= $star ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ $review->created_at->diffForHumans() }}</p>
                        @if($review->comment)
                            <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @else
        <p class="text-gray-600">Sản phẩm này chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
    @endif
</div>