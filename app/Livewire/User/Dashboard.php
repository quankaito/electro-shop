<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class Dashboard extends Component
{
    public $user;
    public $orders;
    public $totalSpent;

    public function mount()
    {
        $this->user = Auth::user();

        // Lấy 10 đơn hàng gần nhất
        $this->orders = Order::where('user_id', $this->user->id)
                             ->orderBy('created_at', 'desc')
                             ->take(10)
                             ->get();

        // Tính tổng số tiền đã chi
        $this->totalSpent = Order::where('user_id', $this->user->id)
                                 ->sum('total_amount');
    }

    public function render()
    {
        // Chỉ cần trả về view Livewire và đặt layout nếu cần
        return view('livewire.user.dashboard')
            ->layout('layouts.app'); 
        // Nếu bạn đang dùng Jetstream, có thể là ->layout('layouts.dashboard')
    }
}
