<div class="p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Top Up Saldo Simpanan</h2>

        <!-- Search -->
        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau NRP..." class="w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NRP</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Simpanan</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->nrp }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($user->getWallet('simpanan')?->balance ?? 0, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button wire:click="openModal({{ $user->user_id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Top Up Saldo
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

        <!-- Transaction Modal -->
        @if($showModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="processTransaction">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Top Up Saldo: {{ $selectedUser->full_name }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="transactionType" class="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                                    <select wire:model="transactionType" id="transactionType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="deposit">Deposit</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                    <input wire:model="amount" type="number" id="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <input wire:model="description" type="text" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Proses
                            </button>
                            <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>