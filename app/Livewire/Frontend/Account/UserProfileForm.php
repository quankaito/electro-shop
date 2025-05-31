<?php

namespace App\Livewire\Frontend\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserProfileForm extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone_number;
    public $avatar; // For new avatar upload (temporary file)
    public $existingAvatar; // Stores the Cloudinary public ID of the current avatar

    public function mount()
    {
        $user = Auth::user();
        $this->name           = $user->name;
        $this->email          = $user->email; // Typically not editable without re-auth
        $this->phone_number   = $user->phone_number;
        $this->existingAvatar = $user->avatar; // We assume `avatar` column stores Cloudinary public ID
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number,' . $user->id,
            'avatar'       => 'nullable|image|max:2048', // 2MB
        ]);

        $updateData = [
            'name'         => $this->name,
            'phone_number' => $this->phone_number,
        ];

        if ($this->avatar) {
            // 1. Xóa avatar cũ trên Cloudinary (nếu có)
            if ($user->avatar) {
                // $user->avatar = ví dụ: "avatars/XyZ123_abcde"
                // Xóa bằng package hoặc Cloudinary API:
                \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::destroy($user->avatar);
            }

            // 2. Upload file mới lên Cloudinary bằng Livewire FileUpload
            $path = $this->avatar->store('avatars', 'cloudinary');
            // → Filament/Livewire FileUpload dùng disk="cloudinary" 
            // nên store(...) trả về public_id như “avatars/XYZabcdef”

            $updateData['avatar'] = $path;
            $this->existingAvatar = $path;
            $this->avatar = null;
        }

        $user->update($updateData);
        $this->dispatch('showToast', message: 'Profile updated successfully!', type: 'success');
    }

    public function render()
    {
        return view('livewire.frontend.account.user-profile-form');
    }
}
