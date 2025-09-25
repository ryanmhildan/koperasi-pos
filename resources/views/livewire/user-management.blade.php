<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Manajemen Pengguna</h2>

                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <x-primary-button wire:click="create">
                            Tambah Pengguna
                        </x-primary-button>
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari pengguna..." class="w-1/3" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NRP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Bergabung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->nrp }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->full_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->roles->first()?->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->join_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="manageCard({{ $user->user_id }})">Card</x-secondary-button>
                                            <x-secondary-button wire:click="edit({{ $user->user_id }})">Edit</x-secondary-button>
                                            <x-danger-button wire:click="delete({{ $user->user_id }})" wire:confirm="Anda yakin ingin menghapus pengguna ini?">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Tidak ada pengguna ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <x-modal name="user-form-modal" :show="$showModal" maxWidth="2xl">
        <x-slot name="title">
            {{ $editMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <div>
                        <x-input-label for="selectedRole" value="Role" />
                        <select wire:model="selectedRole" id="selectedRole" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedRole') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="is_active" value="Status" />
                        <label class="inline-flex items-center mt-2">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
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

    <!-- Manage Card Modal -->
    <x-modal name="card-management-modal" :show="$showCardModal" maxWidth="2xl">
        <x-slot name="title">
            Manajemen Kartu Kredit
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="saveCard">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="card_number" value="Nomor Kartu" />
                        <x-text-input wire:model="card_number" id="card_number" type="text" class="mt-1 block w-full" />
                        @error('card_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="bank_name" value="Nama Bank" />
                        <x-text-input wire:model="bank_name" id="bank_name" type="text" class="mt-1 block w-full" />
                        @error('bank_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="credit_limit" value="Limit Kredit" />
                        <x-text-input wire:model="credit_limit" id="credit_limit" type="number" class="mt-1 block w-full" />
                        @error('credit_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="cash_out_limit" value="Limit Cash Out" />
                        <x-text-input wire:model="cash_out_limit" id="cash_out_limit" type="number" class="mt-1 block w-full" />
                        @error('cash_out_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input-label for="expiry_date" value="Tanggal Kadaluarsa" />
                        <x-text-input wire:model="expiry_date" id="expiry_date" type="text" placeholder="MM/YY" class="mt-1 block w-full" />
                        @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Batal
            </x-secondary-button>

            <x-primary-button class="ml-2" wire:click="saveCard">
                Simpan Kartu
            </x-primary-button>
        </x-slot>
    </x-modal>
</div>