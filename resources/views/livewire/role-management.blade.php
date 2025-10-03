<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Role & Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <x-primary-button wire:click="create()">Tambah Role</x-primary-button>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Izin (Permissions)</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($roles as $role)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $role->name }}</td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                                            @foreach ($role->permissions as $permission)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="edit({{ $role->id }})">Edit</x-secondary-button>
                                            <x-danger-button wire:click="delete({{ $role->id }})" wire:confirm="Apakah Anda yakin ingin menghapus role ini?">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
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
                        @foreach ($permissions as $permission)
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                                       wire:model.defer="selectedPermissions" value="{{ $permission->name }}">
                                <span class="ml-2 text-sm text-gray-600">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="closeModal">
                        Batal
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        {{ $role_id ? 'Simpan Perubahan' : 'Simpan' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>