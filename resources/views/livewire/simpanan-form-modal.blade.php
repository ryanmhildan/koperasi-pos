<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-800 bg-opacity-75">
        <div class="w-full max-w-2xl mx-auto bg-white rounded-lg shadow-lg">
            
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">{{ $modalTitle }}</h2>
                <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-4">
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="user_id_simpanan" :value="__('Anggota')" />
                            <select id="user_id_simpanan" wire:model.defer="user_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Pilih Anggota</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->full_name }} ({{ $user->nrp }})</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-input-label for="transaction_date_simpanan" :value="__('Tanggal Transaksi')" />
                            <x-text-input id="transaction_date_simpanan" type="date" class="block w-full mt-1" wire:model.defer="transaction_date" />
                            @error('transaction_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-input-label for="amount_simpanan" :value="__('Jumlah (Rp)')" />
                            <x-text-input id="amount_simpanan" type="number" step="1000" class="block w-full mt-1" wire:model.defer="amount" placeholder="e.g. 50000" />
                            @error('amount') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-input-label for="description_simpanan" :value="__('Deskripsi')" />
                            <textarea id="description_simpanan" wire:model.defer="description" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3"></textarea>
                            @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <x-secondary-button type="button" wire:click="closeModal" class="mr-4">
                            Batal
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            Simpan
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>