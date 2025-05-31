<a href="{{ route('account.wishlist') }}" class="relative text-gray-600 hover:text-indigo-600" title="Danh sách yêu thích">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
    @if($count > 0)
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-semibold rounded-full px-1.5 py-0.5 leading-tight">
            {{ $count }}
        </span>
    @endif
</a>