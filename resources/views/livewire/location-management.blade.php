
<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Lokasi</h2>

                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-end items-center mb-4">
                        <x-primary-button wire:click="create">
                            Tambah Lokasi
                        </x-primary-button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($locations as $location)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $location->location_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $location->address }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($location->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="edit({{ $location->location_id }})">Edit</x-secondary-button>
                                            <x-danger-button wire:click="delete({{ $location->location_id }})" wire:confirm="Anda yakin ingin menghapus lokasi ini?">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data lokasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $locations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Location Modal -->
    <x-modal name="location-form-modal" :show="$showModal" maxWidth="lg">
        <x-slot name="title">
            {{ $editMode ? 'Edit Lokasi' : 'Tambah Lokasi' }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="space-y-4">
                    <div>
                        <x-input-label for="location_name" value="Nama Lokasi" />
                        <x-text-input wire:model="location_name" id="location_name" type="text" class="mt-1 block w-full" />
                        @error('location_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="address" value="Alamat" />
                        <textarea wire:model="address" id="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-600">Aktif</span>
                        </label>
                    </div>
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Batal
            </x-secondary-button>

            <x-primary-button class="ml-2" wire:click="{{ $editMode ? 'update' : 'store' }}">
                Simpan
            </x-primary-button>
        </x-slot>
    </x-modal>
</div>
