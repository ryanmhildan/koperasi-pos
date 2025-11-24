<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-800 bg-opacity-75">
        <div class="w-full max-w-lg mx-auto bg-white rounded-lg shadow-lg">
            
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Konfirmasi Persetujuan Pinjaman</h2>
                <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-6">
                @if($pinjaman)
                <p class="mb-4">Anda akan menyetujui pinjaman untuk:</p>
                <div class="p-4 mb-4 bg-gray-50 rounded-lg border">
                    <p><strong>Nama:</strong> {{ $pinjaman->user->full_name }}</p>
                    <p><strong>Jumlah:</strong> Rp {{ number_format($pinjaman->loan_amount, 0, ',', '.') }}</p>
                    <p><strong>Tujuan:</strong> {{ $pinjaman->loan_purpose }}</p>
                </div>
                <p>Tindakan ini akan mengaktifkan pinjaman dan mentransfer dana ke anggota. Apakah Anda yakin?</p>
                @else
                <p>Memuat data pinjaman...</p>
                @endif
            </div>

            <div class="flex items-center justify-end p-4 bg-gray-50 border-t rounded-b-lg">
                <x-secondary-button type="button" wire:click="closeModal" class="mr-4">
                    Batal
                </x-secondary-button>
                <x-primary-button type="button" wire:click="approve" wire:loading.attr="disabled">
                    Ya, Setujui
                </x-primary-button>
            </div>

        </div>
    </div>
    @endif
</div>
