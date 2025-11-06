<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class MyAngsuran extends Component
{
    use WithPagination;

    public $pinjaman_id;
    public $amount;
    public $loans;
    public $installments;

    public function mount()
    {
        $this->loans = Pinjaman::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->get();
        $this->installments = collect();
    }

    public function updatedPinjamanId($pinjaman_id)
    {
        if ($pinjaman_id) {
            $this->installments = Angsuran::where('pinjaman_id', $pinjaman_id)->latest()->get();
            $loan = Pinjaman::find($pinjaman_id);
            if ($loan) {
                // Calculate monthly installment
                $interest = $loan->loan_amount * ($loan->interest_rate / 100);
                $this->amount = ($loan->loan_amount + $interest) / $loan->tenor_months;
            }
        } else {
            $this->installments = collect();
            $this->amount = null;
        }
    }

    public function payAngsuran()
    {
        $this->validate([
            'pinjaman_id' => 'required|exists:pinjaman,pinjaman_id',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () {
            $user = Auth::user();
            $loan = Pinjaman::find($this->pinjaman_id);

            if ($loan->remaining_balance <= 0) {
                $this->dispatch('swal:error', [
                    'title' => 'Gagal!',
                    'text' => 'Pinjaman ini sudah lunas.',
                ]);
                return;
            }

            $operasionalWallet = $user->getOrCreateWallet('operasional');

            if ($operasionalWallet->balance() < $this->amount) {
                $this->dispatch('swal:error', [
                    'title' => 'Gagal!',
                    'text' => 'Saldo di wallet operasional tidak mencukupi.',
                ]);
                return;
            }

            // Find the next due installment
            $nextInstallment = Angsuran::where('pinjaman_id', $this->pinjaman_id)
                ->where('status', 'pending')
                ->orderBy('due_date', 'asc')
                ->first();

            if (!$nextInstallment) {
                $this->dispatch('swal:error', [
                    'title' => 'Gagal!',
                    'text' => 'Tidak ada angsuran yang perlu dibayar saat ini.',
                ]);
                return;
            }

            // Transfer to Koperasi Wallet
            $koperasiUser = User::find(1); // Assuming user ID 1 is Koperasi
            $koperasiWallet = $koperasiUser->getOrCreateWallet('utama');
            
            $operasionalWallet->transfer($koperasiWallet, $this->amount, ['description' => 'Pembayaran Angsuran Pinjaman #' . $loan->pinjaman_id]);

            // Process payment on angsuran model
            $nextInstallment->processPayment();
            
            $this->updatedPinjamanId($this->pinjaman_id); // Refresh installments

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Pembayaran angsuran berhasil.',
            ]);
        });
    }

    public function render()
    {
        return view('livewire.my-angsuran');
    }
}
