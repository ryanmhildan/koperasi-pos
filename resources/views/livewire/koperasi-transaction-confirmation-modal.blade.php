<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-800 bg-opacity-75">
        <div class="w-full max-w-lg mx-auto bg-white rounded-lg shadow-lg">
            
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold {{ $action === 'deposit' ? 'text-green-700' : 'text-red-700' }}">
                    Konfirmasi {{ ucfirst($action) }}
                </h2>
                <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-6">
                <p class="mb-4">Anda akan melakukan {{ $action }} dengan detail berikut:</p>
                <div class="p-4 mb-4 bg-gray-50 rounded-lg border">
                    <p><strong>Jumlah:</strong> Rp {{ number_format($amount, 0, ',', '.') }}</p>
                    <p><strong>Deskripsi:</strong> {{ $description }}</p>
                </div>
                <p>Tindakan ini akan mempengaruhi saldo utama koperasi. Apakah Anda yakin?</p>
            </div>

            <div class="flex items-center justify-end p-4 bg-gray-50 border-t rounded-b-lg">
                <x-secondary-button type="button" wire:click="closeModal">
                    Batal
                </x-secondary-button>
                @if($action === 'deposit')
                    <x-primary-button type="button" wire:click="confirm" wire:loading.attr="disabled" class="ml-3 bg-green-600 hover:bg-green-700">
                        Ya, Lanjutkan Deposit
                    </x-primary-button>
                @else
                    <x-danger-button type="button" wire:click="confirm" wire:loading.attr="disabled" class="ml-3">
                        Ya, Lanjutkan Penarikan
                    </x-danger-button>
                @endif
            </div>

        </div>
    </div>
    @endif
</div>
