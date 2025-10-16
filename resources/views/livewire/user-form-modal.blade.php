<div>
    <x-modal name="user-form-modal" maxWidth="2xl">
        <div class="p-6">
             <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}
            </h2>

            <form wire:submit.prevent="save">
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="nrp" value="NRP" />
                        <x-text-input wire:model="nrp" id="nrp" type="text" class="mt-1 block w-full" />
                        @error('nrp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="username" value="Username" />
                        <x-text-input wire:model="username" id="username" type="text" class="mt-1 block w-full" />
                        @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="full_name" value="Nama Lengkap" />
                        <x-text-input wire:model="full_name" id="full_name" type="text" class="mt-1 block w-full" />
                        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="email" value="Email" />
                        <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="phone" value="Telepon" />
                        <x-text-input wire:model="phone" id="phone" type="text" class="mt-1 block w-full" />
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="join_date" value="Tanggal Bergabung" />
                        <x-text-input wire:model="join_date" id="join_date" type="date" class="mt-1 block w-full" />
                        @error('join_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input wire:model="password" id="password" type="password" class="mt-1 block w-full" />
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" class="mt-1 block w-full" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="selectedRoles" value="Roles" />
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($allRoles as $role)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           wire:model="selectedRoles" value="{{ $role->name }}">
                                    <span class="ml-2 text-sm text-gray-600">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedRoles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="is_active" value="Status" />
                        <label class="inline-flex items-center mt-2">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button type="button" wire:click="closeModal">
                        Batal
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        Simpan
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
