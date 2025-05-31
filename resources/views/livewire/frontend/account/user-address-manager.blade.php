<div>
    {{-- Hiển thị flash message nếu có --}}
    @if(session()->has('message'))
        <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">My Addresses</h2>
        <button wire:click="openAddModal" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add New Address
        </button>
    </div>

    {{-- Danh sách địa chỉ hiện có --}}
    <div class="space-y-4">
        @forelse ($addresses as $address)
            <div class="border p-4 rounded shadow-sm" wire:key="address-{{ $address->id }}">
                <h3 class="font-semibold">{{ $address->full_name }}</h3>
                <p>{{ $address->address_line1 }}</p>
                @if($address->address_line2)
                    <p>{{ $address->address_line2 }}</p>
                @endif
                <p>
                    {{ $address->ward?->name }}, 
                    {{ $address->district?->name }}, 
                    {{ $address->province?->name }}
                </p>
                <p>Phone: {{ $address->phone_number }}</p>
                @if($address->postal_code)
                    <p>Postal Code: {{ $address->postal_code }}</p>
                @endif

                <div class="mt-2 space-x-2">
                    @if($address->is_default_shipping)
                        <span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded-full">Default Shipping</span>
                    @else
                        <button wire:click="setDefault({{ $address->id }}, 'shipping')" 
                                class="text-xs text-blue-600 hover:underline">
                            Set as Default Shipping
                        </button>
                    @endif

                    @if($address->is_default_billing)
                        <span class="px-2 py-1 text-xs bg-indigo-200 text-indigo-800 rounded-full">Default Billing</span>
                    @else
                        <button wire:click="setDefault({{ $address->id }}, 'billing')" 
                                class="text-xs text-indigo-600 hover:underline">
                            Set as Default Billing
                        </button>
                    @endif
                </div>

                <div class="mt-3 flex space-x-3">
                    <button wire:click="openEditModal({{ $address->id }})" 
                            class="text-sm text-yellow-600 hover:underline">
                        Edit
                    </button>
                    <button wire:click="deleteAddress({{ $address->id }})"
                            wire:confirm="Are you sure you want to delete this address?"
                            class="text-sm text-red-600 hover:underline">
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <p>You have no saved addresses.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $addresses->links() }}
    </div>

    {{-- Add/Edit Address Modal --}}
    @if($showAddressModal)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
                <form wire:submit.prevent="saveAddress">
                    <h3 class="text-xl font-semibold mb-4">
                        {{ $isEditing ? 'Edit Address' : 'Add New Address' }}
                    </h3>

                    {{-- Full Name & Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">
                                Full Name
                            </label>
                            <input type="text"
                                   wire:model.defer="full_name"
                                   id="full_name"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('full_name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">
                                Phone Number
                            </label>
                            <input type="text"
                                   wire:model.defer="phone_number"
                                   id="phone_number"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('phone_number')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Address Line 1 & 2 --}}
                    <div class="mt-4">
                        <label for="address_line1" class="block text-sm font-medium text-gray-700">
                            Address Line 1
                        </label>
                        <input type="text"
                               wire:model.defer="address_line1"
                               id="address_line1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('address_line1')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="address_line2" class="block text-sm font-medium text-gray-700">
                            Address Line 2 (Optional)
                        </label>
                        <input type="text"
                               wire:model.defer="address_line2"
                               id="address_line2"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('address_line2')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Province / District / Ward --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        {{-- Province --}}
                        <div>
                            <label for="province_id" class="block text-sm font-medium text-gray-700">
                                Province/City
                            </label>
                            <select wire:model="province_id"
                                    id="province_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('province_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- District --}}
                        <div>
                            <label for="district_id" class="block text-sm font-medium text-gray-700">
                                District/County
                            </label>
                            <select wire:model="district_id"
                                    id="district_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    @disabled(empty($province_id))>
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Ward --}}
                        <div>
                            <label for="ward_id" class="block text-sm font-medium text-gray-700">
                                Ward/Commune
                            </label>
                            <select wire:model="ward_id"
                                    id="ward_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    @disabled(empty($district_id))>
                                <option value="">Select Ward</option>
                                @foreach($wards as $ward)
                                    <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                @endforeach
                            </select>
                            @error('ward_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Postal Code --}}
                    <div class="mt-4">
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                            Postal Code (Optional)
                        </label>
                        <input type="text"
                               wire:model.defer="postal_code"
                               id="postal_code"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('postal_code')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Default Shipping / Billing --}}
                    <div class="mt-6 space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model.defer="is_default_shipping"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Set as default shipping address</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model.defer="is_default_billing"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Set as default billing address</span>
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <span wire:loading.remove wire:target="saveAddress">
                                {{ $isEditing ? 'Update Address' : 'Save Address' }}
                            </span>
                            <span wire:loading wire:target="saveAddress">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
