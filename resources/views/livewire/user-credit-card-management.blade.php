<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kartu Kredit Anggota') }}
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

                    <div class="flex justify-between items-center mb-4">
                        <x-primary-button wire:click="create">
                            Tambah Kartu
                        </x-primary-button>
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, NRP, atau no. kartu..." class="w-1/3" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Kartu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Limit Kredit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo Terpakai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Limit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($cards as $card)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $card->user->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $card->user->nrp }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $card->card_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($card->credit_limit, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($card->current_balance, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowp font-semibold text-blue-600">Rp {{ number_format($card->credit_limit - $card->current_balance, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($card->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-secondary-button wire:click="edit({{ $card->card_id }})">Edit</x-secondary-button>
                                            <x-danger-button wire:click="confirmCardDeletion({{ $card->card_id }})">Hapus</x-danger-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data kartu kredit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $cards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Card Modal -->
    <x-modal name="card-form-modal" maxWidth="2xl">
        <form wire:submit.prevent="confirmCardSave" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Edit Kartu Kredit' : 'Tambah Kartu Kredit' }}
            </h2>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="user_id" value="Anggota" />
                    <select wire:model="user_id" id="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $editMode ? 'disabled' : '' }}>
                        <option value="">Pilih Anggota</option>
                        @foreach($users as $user)
                            <option value="{{ $user->user_id }}">{{ $user->full_name }} ({{$user->nrp}})</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="card_number" value="Nomor Kartu" />
                    <x-text-input wire:model="card_number" id="card_number" type="text" class="mt-1 block w-full" />
                    @error('card_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                    <x-input-label for="bank_name" value="Nama Bank" />
                    <x-text-input wire:model="bank_name" id="bank_name" type="text" class="mt-1 block w-full" />
                    @error('bank_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input-label for="expiry_date" value="Tanggal Kadaluarsa (MM/YY)" />
                    <x-text-input wire:model="expiry_date" id="expiry_date" type="text" placeholder="Contoh: 12/28" class="mt-1 block w-full" />
                    @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @if($editMode)
                <div>
                    <x-input-label for="current_balance" value="Saldo Terpakai" />
                    <x-text-input wire:model="current_balance" id="current_balance" type="number" class="mt-1 block w-full bg-gray-100" disabled />
                </div>
                @endif
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm">
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
    </x-modal>

    <x-confirmation-modal id="confirm-card-deletion" wire:model.live="confirmingCardDeletion">
        <x-slot name="title">
            Hapus Kartu Kredit
        </x-slot>

        <x-slot name="content">
            Apakah Anda yakin ingin menghapus kartu ini? Tindakan ini tidak dapat dibatalkan.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingCardDeletion', false)" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteCard" wire:loading.attr="disabled">
                Hapus Kartu
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal id="confirm-card-save" wire:model.live="confirmingCardSave">
        <x-slot name="title">
            Simpan Kartu Kredit
        </x-slot>

        <x-slot name="content">
            Apakah Anda yakin ingin menyimpan perubahan pada kartu ini?
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingCardSave', false)" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>

            <x-primary-button class="ml-3" wire:click="{{ $editMode ? 'update' : 'store' }}" wire:loading.attr="disabled">
                Simpan
            </x-primary-button>
        </x-slot>
    </x-confirmation-modal>
</div>