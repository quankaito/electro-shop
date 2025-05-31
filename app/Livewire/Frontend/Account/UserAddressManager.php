<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Address;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserAddressManager extends Component
{
    use WithPagination;

    // Biến điều khiển modal
    public $showAddressModal = false;
    public $isEditing       = false;
    public $addressIdToEdit = null;

    // Các field trong form Add/Edit
    public $full_name;
    public $phone_number;
    public $address_line1;
    public $address_line2;
    public $province_id;
    public $district_id;
    public $ward_id;
    public $postal_code;
    public $is_default_shipping = false;
    public $is_default_billing  = false;

    // Dữ liệu cho dropdown
    public $provinces = [];
    public $districts = [];
    public $wards     = [];

    protected $rules = [
        'full_name'       => 'required|string|max:255',
        'phone_number'    => 'required|string|max:15',
        'address_line1'   => 'required|string|max:255',
        'address_line2'   => 'nullable|string|max:255',
        'province_id'     => 'required|exists:provinces,id',
        'district_id'     => 'required|exists:districts,id',
        'ward_id'         => 'required|exists:wards,id',
        'postal_code'     => 'nullable|string|max:10',
        'is_default_shipping' => 'boolean',
        'is_default_billing'  => 'boolean',
    ];

    public function mount()
    {
        // Load sẵn list provinces
        $this->provinces = Province::orderBy('name')->get();

        // Mặc định districts & wards rỗng
        $this->districts = collect();
        $this->wards     = collect();
    }

    /**
     * Khi user chọn Province → reset district & ward → load lại districts tương ứng
     */
    public function updatedProvinceId($value)
    {
        $this->district_id = null;
        $this->ward_id     = null;

        $this->loadDistricts();
        $this->wards = collect(); // đảm bảo wards rỗng sau khi reset
    }

    /**
     * Khi user chọn District → reset ward → load lại wards tương ứng
     */
    public function updatedDistrictId($value)
    {
        $this->ward_id = null;
        $this->loadWards();
    }

    /**
     * Load danh sách districts theo $this->province_id
     */
    public function loadDistricts()
    {
        if ($this->province_id) {
            $this->districts = District::where('province_id', $this->province_id)
                                       ->orderBy('name')
                                       ->get();
        } else {
            $this->districts = collect();
        }
    }

    /**
     * Load danh sách wards theo $this->district_id
     */
    public function loadWards()
    {
        if ($this->district_id) {
            $this->wards = Ward::where('district_id', $this->district_id)
                               ->orderBy('name')
                               ->get();
        } else {
            $this->wards = collect();
        }
    }

    /**
     * Mở modal để Add Address
     */
    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showAddressModal = true;
    }

    /**
     * Mở modal để Edit Address, preload dữ liệu cũ
     */
    public function openEditModal($addressId)
    {
        $address = Auth::user()->addresses()->findOrFail($addressId);

        $this->addressIdToEdit = $address->id;
        $this->full_name       = $address->full_name;
        $this->phone_number    = $address->phone_number;
        $this->address_line1   = $address->address_line1;
        $this->address_line2   = $address->address_line2;
        $this->province_id     = $address->province_id;

        // 1. Load districts sau khi đã gán province_id
        $this->loadDistricts();
        $this->district_id = $address->district_id;

        // 2. Load wards sau khi đã gán district_id
        $this->loadWards();
        $this->ward_id = $address->ward_id;

        $this->postal_code = $address->postal_code;
        $this->is_default_shipping = $address->is_default_shipping;
        $this->is_default_billing  = $address->is_default_billing;

        $this->isEditing = true;
        $this->showAddressModal = true;
    }

    /**
     * Lưu Address (Create hoặc Update)
     */
    public function saveAddress()
    {
        $this->validate();

        $user = Auth::user();
        $data = [
            'full_name'       => $this->full_name,
            'phone_number'    => $this->phone_number,
            'address_line1'   => $this->address_line1,
            'address_line2'   => $this->address_line2,
            'province_id'     => $this->province_id,
            'district_id'     => $this->district_id,
            'ward_id'         => $this->ward_id,
            'postal_code'     => $this->postal_code,
            'is_default_shipping' => $this->is_default_shipping,
            'is_default_billing'  => $this->is_default_billing,
        ];

        // Nếu đánh dấu default shipping, unset các địa chỉ default cũ
        if ($this->is_default_shipping) {
            $user->addresses()->update(['is_default_shipping' => false]);
        }
        // Nếu đánh dấu default billing, unset các địa chỉ default cũ
        if ($this->is_default_billing) {
            $user->addresses()->update(['is_default_billing' => false]);
        }

        if ($this->isEditing) {
            // Cập nhật
            $address = $user->addresses()->findOrFail($this->addressIdToEdit);
            $address->update($data);
            session()->flash('message', 'Address updated successfully!');
        } else {
            // Tạo mới
            $user->addresses()->create($data);
            session()->flash('message', 'Address added successfully!');
        }

        $this->closeModal();
    }

    /**
     * Xóa Address
     */
    public function deleteAddress($addressId)
    {
        $address = Auth::user()->addresses()->findOrFail($addressId);
        $address->delete();
        session()->flash('message', 'Address deleted successfully!');
    }

    /**
     * Đặt default shipping hoặc billing
     */
    public function setDefault($addressId, $type)
    {
        $user = Auth::user();
        $field = 'is_default_' . $type;

        // Unset tất cả cũ
        $user->addresses()->update([$field => false]);

        // Set mới
        $address = $user->addresses()->findOrFail($addressId);
        $address->update([$field => true]);

        session()->flash('message', "Default {$type} address updated!");
    }

    /**
     * Đóng modal và reset form
     */
    public function closeModal()
    {
        $this->showAddressModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->resetValidation();

        $this->addressIdToEdit    = null;
        $this->full_name          = '';
        $this->phone_number       = '';
        $this->address_line1      = '';
        $this->address_line2      = '';
        $this->province_id        = null;
        $this->district_id        = null;
        $this->ward_id            = null;
        $this->postal_code        = '';
        $this->is_default_shipping = false;
        $this->is_default_billing  = false;

        $this->districts = collect();
        $this->wards     = collect();
    }

    public function render()
    {
        $addresses = Auth::user()
                         ->addresses()
                         ->with(['province', 'district', 'ward'])
                         ->latest()
                         ->paginate(5);

        return view('livewire.frontend.account.user-address-manager', [
            'addresses' => $addresses,
        ]);
    }
}
