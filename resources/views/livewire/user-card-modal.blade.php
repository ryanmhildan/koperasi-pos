<div>
    <x-modal name="card-management-modal" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Manajemen Kartu Kredit
            </h2>

            <form wire:submit.prevent="saveCard">
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <div class="mt-6 flex justify-end">
                    <x-secondary-button type="button" wire:click="closeModal">
                        Batal
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        Simpan Kartu
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
