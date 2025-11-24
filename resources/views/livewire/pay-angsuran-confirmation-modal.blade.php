<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-800 bg-opacity-75">
        <div class="w-full max-w-lg mx-auto bg-white rounded-lg shadow-lg">
            
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Konfirmasi Pembayaran Angsuran</h2>
                <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-6">
                @if($pinjaman)
                    <p class="mb-4">Anda akan membayar angsuran untuk:</p>
                    <div class="p-4 mb-4 bg-gray-50 rounded-lg border">
                        <p><strong>Peminjam:</strong> {{ $pinjaman->user->full_name }}</p>
                        <p><strong>Pinjaman ID:</strong> #{{ $pinjaman->pinjaman_id }}</p>
                        <p><strong>Jumlah Pembayaran:</strong> Rp {{ number_format($amount, 0, ',', '.') }}</p>
                    </div>
                    <p>Pastikan saldo Anda mencukupi. Apakah Anda yakin?</p>
                @else
                    <p>Memuat data pinjaman...</p>
                @endif
            </div>

            <div class="flex items-center justify-end p-4 bg-gray-50 border-t rounded-b-lg">
                <x-secondary-button type="button" wire:click="closeModal">
                    Batal
                </x-secondary-button>
                <x-primary-button type="button" wire:click="confirm" wire:loading.attr="disabled" class="ml-3">
                    Ya, Bayar
                </x-primary-button>
            </div>

        </div>
    </div>
    @endif
</div>
