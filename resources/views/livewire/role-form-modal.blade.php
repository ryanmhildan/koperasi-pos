<div>
    <x-modal name="role-modal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $role_id ? 'Edit Role' : 'Buat Role Baru' }}
            </h2>

            <form wire:submit.prevent="store">
                <div class="mt-4">
                    <x-input-label for="name" :value="__('Nama Role')" />
                    <x-text-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mt-4">
                    <x-input-label :value="__('Izin (Permissions)')" />
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($allPermissions as $permission)
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                       wire:model="selectedPermissions" value="{{ $permission->name }}">
                                <span class="ml-2 text-sm text-gray-600">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button type="button" wire:click="closeModal">
                        Batal
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        {{ $role_id ? 'Simpan Perubahan' : 'Simpan' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
