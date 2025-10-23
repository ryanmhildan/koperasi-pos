<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kartu Kredit Saya') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($card)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 bg-gray-800 text-white p-6 rounded-lg shadow-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-2xl font-bold">{{ $card->bank_name }}</span>
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h2m4 0h2m-8 4h14M3 6h18v12H3z"></path></svg>
                                </div>
                                <div class="mt-8 text-2xl font-mono tracking-wider">
                                    {{ chunk_split($card->card_number, 4, ' ') }}
                                </div>
                                <div class="mt-6 flex justify-between items-end">
                                    <div>
                                        <p class="text-xs uppercase">Card Holder</p>
                                        <p class="font-medium">{{ $card->user->full_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase">Expires</p>
                                        <p class="font-medium">{{ $card->expiry_date }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Detail Limit</h3>
                                <div class="mt-2 space-y-2">
                                    <p><strong>Limit Kredit:</strong> Rp {{ number_format($card->credit_limit, 0, ',', '.') }}</p>
                                    <p><strong>Saldo Terpakai:</strong> Rp {{ number_format($card->current_balance, 0, ',', '.') }}</p>
                                    <p class="font-bold text-blue-600"><strong>Sisa Limit:</strong> Rp {{ number_format($card->credit_limit - $card->current_balance, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Status</h3>
                                <div class="mt-2">
                                    @if ($card->is_active)
                                        <p class="text-green-600 font-bold">Aktif</p>
                                    @else
                                        <p class="text-red-600 font-bold">Non-Aktif</p>
                                        <p class="text-sm text-gray-600">Hubungi admin untuk mengaktifkan kartu Anda.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <p>Anda tidak memiliki kartu kredit terdaftar. Silakan hubungi admin.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>