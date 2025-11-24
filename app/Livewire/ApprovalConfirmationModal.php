<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Pinjaman;
use App\Models\User;
use App\Models\Angsuran;

class ApprovalConfirmationModal extends Component
{
    public $isOpen = false;
    public $pinjamanId;
    public $pinjaman;

    #[On('openApprovalModal')]
    public function openModal($pinjamanId)
    {
        $this->pinjamanId = $pinjamanId;
        $this->pinjaman = Pinjaman::with('user')->find($pinjamanId);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->pinjaman = null;
        $this->pinjamanId = null;
    }

    public function approve()
    {
        if (!$this->pinjaman) {
            // Handle case where pinjaman is not found
            session()->flash('error', 'Pinjaman tidak ditemukan.');
            $this->closeModal();
            return;
        }

        if ($this->pinjaman->status !== 'pending') {
            session()->flash('error', 'Pinjaman ini tidak bisa disetujui.');
            $this->closeModal();
            return;
        }
        
        $this->pinjaman->status = 'active';
        $this->pinjaman->loan_date = now();
        $this->pinjaman->save();

        // Deposit to user's simpanan wallet
        $user = $this->pinjaman->user;
        $simpananWallet = $user->getWallet('simpanan');
        if (!$simpananWallet) {
            $user->createWallet(['name' => 'simpanan', 'slug' => 'simpanan']);
            $simpananWallet = $user->getWallet('simpanan');
        }
        $simpananWallet->deposit($this->pinjaman->loan_amount, ['description' => 'Pencairan Pinjaman: ' . $this->pinjaman->loan_purpose, 'reference_id' => $this->pinjaman->pinjaman_id]);

        // Generate installment schedule
        $this->generateAngsuranSchedule($this->pinjaman);

        session()->flash('message', 'Pinjaman berhasil disetujui.');
        
        $this->closeModal();
        $this->dispatch('loanApproved');
    }

    private function calculateMonthlyPayment($principal, $interest_rate, $months)
    {
        $rate = $interest_rate / 100 / 12;
        if ($rate > 0) {
            return $principal * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
        }
        return $principal / $months;
    }

    private function generateAngsuranSchedule($pinjaman)
    {
        Angsuran::where('pinjaman_id', $pinjaman->pinjaman_id)->delete();
        $monthlyPayment = $this->calculateMonthlyPayment($pinjaman->loan_amount, $pinjaman->interest_rate, $pinjaman->tenor_months);
        $dueDate = \Carbon\Carbon::parse($pinjaman->loan_date);
        for ($i = 1; $i <= $pinjaman->tenor_months; $i++) {
            $dueDate->addMonth();
            Angsuran::create([
                'pinjaman_id' => $pinjaman->pinjaman_id,
                'amount' => $monthlyPayment,
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => 'pending',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.approval-confirmation-modal');
    }
}
