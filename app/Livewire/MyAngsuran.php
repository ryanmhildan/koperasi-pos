<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Pinjaman;
use App\Models\Angsuran;
use Illuminate\Support\Facades\Auth;
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

        $loan = Pinjaman::find($this->pinjaman_id);

        if ($loan->remaining_balance <= 0) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Pinjaman ini sudah lunas.',
            ]);
            return;
        }

        // Find the next due installment or create a new one if none exists
        $nextInstallment = Angsuran::where('pinjaman_id', $this->pinjaman_id)
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->first();

        if ($nextInstallment) {
            $nextInstallment->processPayment();
        } else {
            // This case should ideally not happen if installments are generated correctly
            // But as a fallback, we can create a new payment record.
            Angsuran::create([
                'pinjaman_id' => $this->pinjaman_id,
                'amount' => $this->amount,
                'due_date' => now(), // Or a calculated due date
                'paid_date' => now(),
                'status' => 'paid',
            ]);

            // Update loan balance
            $loan->remaining_balance -= $this->amount;
            $loan->total_paid += $this->amount;
            if ($loan->remaining_balance <= 0) {
                $loan->status = 'paid_off';
            }
            $loan->save();
        }

        $this->updatedPinjamanId($this->pinjaman_id); // Refresh installments

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pembayaran angsuran berhasil.',
        ]);
    }

    public function render()
    {
        return view('livewire.my-angsuran');
    }
}
