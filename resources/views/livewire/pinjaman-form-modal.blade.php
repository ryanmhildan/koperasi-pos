<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-900 bg-opacity-50">
            <div class="w-11/12 max-w-3xl mx-auto bg-white rounded-lg shadow-lg">
                <div class="flex items-center justify-between p-4 border-b">
                    <h4 class="text-lg font-semibold">{{ $editMode ? 'Ubah Pinjaman' : 'Tambah Pinjaman' }}</h4>
                    <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="p-4">
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="mb-4">
                                <x-input-label for="user_id_modal" :value="__('Anggota')" />
                                <select id="user_id_modal" wire:model="user_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option>Pilih Anggota</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            <div class="mb-4">
                                <x-input-label for="loan_date_modal" :value="__('Tgl Pinjaman')" />
                                <x-text-input id="loan_date_modal" wire:model="loan_date" type="date" class="block w-full mt-1" />
                                @error('loan_date') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            <div class="mb-4">
                                <x-input-label for="loan_amount_modal" :value="__('Jumlah Pinjaman')" />
                                <x-text-input id="loan_amount_modal" wire:model="loan_amount" type="number" step="1000" class="block w-full mt-1" />
                                @error('loan_amount') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            <div class="mb-4">
                                <x-input-label for="interest_rate_modal" :value="__('Suku Bunga (% per tahun)')" />
                                <x-text-input id="interest_rate_modal" wire:model="interest_rate" type="number" step="0.1" class="block w-full mt-1" />
                                @error('interest_rate') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            <div class="mb-4">
                                <x-input-label for="tenor_months_modal" :value="__('Tenor (Bulan)')" />
                                <x-text-input id="tenor_months_modal" wire:model="tenor_months" type="number" class="block w-full mt-1" />
                                @error('tenor_months') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            <div class="mb-4">
                                <x-input-label for="loan_type_modal" :value="__('Tipe Pinjaman')" />
                                <select id="loan_type_modal" wire:model="loan_type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach($loanTypes as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('loan_type') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            @if($editMode)
                            <div class="mb-4">
                                <x-input-label for="status_modal" :value="__('Status')" />
                                <select id="status_modal" wire:model="status" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="pending">Pending</option>
                                    <option value="active">Aktif</option>
                                    <option value="paid">Lunas</option>
                                    <option value="overdue">Jatuh Tempo</option>
                                </select>
                                @error('status') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                            @endif
                        </div>
                        <div class="mb-4">
                            <x-input-label for="loan_purpose_modal" :value="__('Tujuan Pinjaman')" />
                            <textarea id="loan_purpose_modal" wire:model="loan_purpose" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            @error('loan_purpose') <span class="text-sm text-red-600">{{ $message }}</span>@enderror
                        </div>
                        <div class="flex items-center justify-end mt-6">
                             <x-secondary-button type="button" wire:click="closeModal" class="mr-4">
                                Batal
                            </x-secondary-button>
                            <x-primary-button type="submit">
                                {{ $editMode ? 'Update' : 'Simpan' }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
