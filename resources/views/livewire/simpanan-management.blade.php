<div class="p-6 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Manajemen Simpanan</h2>
        <button wire:click="create()" class="px-4 py-2 font-bold text-white bg-blue-500 rounded-full hover:bg-blue-700">
            Tambah Simpanan
        </button>
    </div>

    @if (session()->has('message'))
        <div class="px-4 py-3 mb-4 text-green-900 bg-green-100 border-t-4 border-green-500 rounded-b shadow-md" role="alert">
            <div class="flex">
                <div>
                    <p class="font-bold">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, NRP, atau deskripsi..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Pengguna</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Tanggal</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Deskripsi</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-right text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Jumlah</th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($simpanan as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $item->user->full_name }}</p>
                            <p class="text-gray-600 whitespace-no-wrap">{{ $item->user->nrp }}</p>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ \Carbon\Carbon::parse($item->transaction_date)->isoFormat('D MMMM YYYY') }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $item->description }}</td>
                        <td class="px-5 py-5 text-sm text-right bg-white border-b border-gray-200">Rp {{ number_format($item->amount, 2, ', ', '.') }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <button wire:click="edit({{ $item->id }})" class="px-3 py-1 font-semibold leading-tight text-yellow-900 bg-yellow-200 rounded-full hover:bg-yellow-300">Ubah</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Anda yakin ingin menghapus data ini?" class="px-3 py-1 font-semibold leading-tight text-red-900 bg-red-200 rounded-full hover:bg-red-300">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-500">Tidak ada data simpanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white border-t">
            {{ $simpanan->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-gray-900 bg-opacity-50">
            <div class="w-11/12 max-w-2xl mx-auto bg-white rounded-lg shadow-lg">
                <div class="flex items-center justify-between p-4 border-b">
                    <h4 class="text-lg font-semibold">{{ $editMode ? 'Ubah Simpanan' : 'Tambah Simpanan' }}</h4>
                    <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="p-4">
                    <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                        <div class="mb-4">
                            <label for="user_id" class="block mb-2 text-sm font-bold text-gray-700">Anggota</label>
                            <select wire:model="user_id" id="user_id" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                                <option value="">Pilih Anggota</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->full_name }} ({{ $user->nrp }})</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="amount" class="block mb-2 text-sm font-bold text-gray-700">Jumlah</label>
                            <input wire:model="amount" type="number" id="amount" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                            @error('amount') <span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="transaction_date" class="block mb-2 text-sm font-bold text-gray-700">Tanggal Transaksi</label>
                            <input wire:model="transaction_date" type="date" id="transaction_date" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                            @error('transaction_date') <span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block mb-2 text-sm font-bold text-gray-700">Deskripsi</label>
                            <textarea wire:model="description" id="description" rows="4" class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"></textarea>
                            @error('description') <span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">{{ $editMode ? 'Update' : 'Simpan' }}</button>
                            <button type="button" wire:click="closeModal()" class="px-4 py-2 ml-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>