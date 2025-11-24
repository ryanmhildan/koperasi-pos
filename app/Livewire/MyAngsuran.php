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
    public ?Pinjaman $pinjaman = null;
    public $amount;
    public $loans;
    public $installments;

    protected $listeners = ['paymentConfirmed' => 'confirmPayAngsuran'];

    public function mount()
    {
        $this->loans = Pinjaman::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();
        $this->installments = collect();
    }

    public function updatedPinjamanId($pinjaman_id)
    {
        if ($pinjaman_id) {
            $this->pinjaman = Pinjaman::find($pinjaman_id);
            $this->installments = Angsuran::where('pinjaman_id', $pinjaman_id)->latest()->get();
            if ($this->pinjaman) {
                // Calculate monthly installment
                $interest = $this->pinjaman->loan_amount * ($this->pinjaman->interest_rate / 100);
                $this->amount = ($this->pinjaman->loan_amount + $interest) / $this->pinjaman->tenor_months;
            }
        } else {
            $this->pinjaman = null;
            $this->installments = collect();
            $this->amount = null;
        }
    }

    public function payAngsuran()
    {
        $this->authorize('pay angsuran');

        $this->validate([
            'pinjaman_id' => 'required|exists:pinjaman,pinjaman_id',
            'amount' => 'required|numeric|min:1',
        ]);

        $this->dispatch('openPayAngsuranModal', pinjaman_id: $this->pinjaman_id, amount: $this->amount);
    }

    public function confirmPayAngsuran()
    {
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

            $simpananWallet = $user->getWallet('simpanan');
            if (!$simpananWallet) {
                $simpananWallet = $user->createWallet(['name' => 'Simpanan', 'slug' => 'simpanan']);
            }

            if ($simpananWallet->balance < $this->amount) {
                $this->dispatch('swal:error', [
                    'title' => 'Gagal!',
                    'text' => 'Saldo di wallet simpanan tidak mencukupi.',
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
            $koperasiUser = User::where('username', 'koperasi')->first();
            $koperasiWallet = $koperasiUser->getWallet('utama');
            if (!$koperasiWallet) {
                $koperasiWallet = $koperasiUser->createWallet(['name' => 'Utama', 'slug' => 'utama']);
            }
            
            $simpananWallet->transfer($koperasiWallet, $this->amount, ['description' => 'Pembayaran Angsuran Pinjaman #' . $loan->pinjaman_id]);

            // Update installment status
            $nextInstallment->status = 'paid';
            $nextInstallment->paid_date = now();
            $nextInstallment->save();

            // Recalculate remaining balance
            $loan->recalculateRemainingBalance();
            
            $this->updatedPinjamanId($this->pinjaman_id); // Refresh installments
            $this->pinjaman->refresh(); // Refresh pinjaman

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
