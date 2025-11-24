<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-800 bg-opacity-75">
        <div class="w-full max-w-lg mx-auto bg-white rounded-lg shadow-lg">
            
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Konfirmasi Pembayaran Angsuran</h2>
                <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-6">
                @if($angsuran)
                    <p class="mb-4">Anda akan menandai lunas angsuran untuk:</p>
                    <div class="p-4 mb-4 bg-gray-50 rounded-lg border">
                        <p><strong>Peminjam:</strong> {{ $angsuran->pinjaman->user->full_name }}</p>
                        <p><strong>Pinjaman ID:</strong> #{{ $angsuran->pinjaman_id }}</p>
                        <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($angsuran->due_date)->isoFormat('D MMM YYYY') }}</p>
                        <p><strong>Jumlah:</strong> Rp {{ number_format($angsuran->amount, 0, ',', '.') }}</p>
                    </div>
                    <p>Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin?</p>
                @else
                    <p>Memuat data angsuran...</p>
                @endif
            </div>

            <div class="flex items-center justify-end p-4 bg-gray-50 border-t rounded-b-lg">
                <x-secondary-button type="button" wire:click="closeModal">
                    Batal
                </x-secondary-button>
                <x-primary-button type="button" wire:click="markAsPaid" wire:loading.attr="disabled" class="ml-3">
                    Ya, Tandai Lunas
                </x-primary-button>
            </div>

        </div>
    </div>
    @endif
</div>
