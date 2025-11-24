<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-800 bg-opacity-75">
        <div class="w-full max-w-lg mx-auto bg-white rounded-lg shadow-lg">
            
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-red-700">Konfirmasi Hapus Pinjaman</h2>
                <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-6">
                @if($pinjaman)
                    <p class="mb-4">Anda akan menghapus pinjaman untuk:</p>
                    <div class="p-4 mb-4 bg-gray-50 rounded-lg border">
                        <p><strong>Nama:</strong> {{ $pinjaman->user->full_name }}</p>
                        <p><strong>Jumlah:</strong> Rp {{ number_format($pinjaman->loan_amount, 0, ',', '.') }}</p>
                        <p><strong>Status:</strong> <span class="font-semibold">{{ $pinjaman->status_text }}</span></p>
                    </div>
                    @if($pinjaman->status !== 'pending')
                        <p class="text-red-600">Hanya pinjaman dengan status "Pending" yang dapat dihapus. Tindakan ini tidak dapat dilanjutkan.</p>
                    @else
                        <p>Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin?</p>
                    @endif
                @else
                    <p>Memuat data pinjaman...</p>
                @endif
            </div>

            <div class="flex items-center justify-end p-4 bg-gray-50 border-t rounded-b-lg">
                <x-secondary-button type="button" wire:click="closeModal">
                    Batal
                </x-secondary-button>
                @if($pinjaman && $pinjaman->status === 'pending')
                    <x-danger-button type="button" wire:click="delete" wire:loading.attr="disabled" class="ml-3">
                        Ya, Hapus
                    </x-danger-button>
                @endif
            </div>

        </div>
    </div>
    @endif
</div>
