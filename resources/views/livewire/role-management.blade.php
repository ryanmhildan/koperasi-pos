<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Role & Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8"> {{-- Changed max-w-7xl to max-w-full --}}
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
                                            <x-danger-button wire:click="confirmRoleDeletion({{ $role->id }})">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $roles->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal id="confirm-role-deletion" wire:model.live="confirmingRoleDeletion">
        <x-slot name="title">
            Hapus Role
        </x-slot>

        <x-slot name="content">
            Apakah Anda yakin ingin menghapus role ini? Tindakan ini tidak dapat dibatalkan.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingRoleDeletion', false)" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteRole" wire:loading.attr="disabled">
                Hapus Role
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
