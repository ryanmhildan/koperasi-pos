<?php

namespace App\Livewire;

use App\Models\Angsuran;
use App\Models\UserCreditCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AngsuranManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $pinjamanId;

    protected $listeners = ['angsuranPaid' => '$refresh'];

    public function mount($pinjamanId = null)
    {
        $this->pinjamanId = $pinjamanId;
    }

    public function pay($angsuranId)
    {
        DB::beginTransaction();

        try {
            $angsuran = Angsuran::with('pinjaman.user')
                ->findOrFail($angsuranId);

            // Ensure the user is paying their own installment
            if ($angsuran->pinjaman->user_id !== Auth::id()) {
                throw new \Exception("Unauthorized action.");
            }

            if (!in_array($angsuran->status, ['pending', 'overdue'])) {
                throw new \Exception("Angsuran ini sudah dibayar atau statusnya tidak valid.");
            }

            $userCreditCard = UserCreditCard::where('user_id', Auth::id())->firstOrFail();
            
            // Recalculate denda just in case
            $angsuran->denda = $angsuran->calculateDenda();
            $totalToPay = $angsuran->total_amount;

            if ($userCreditCard->current_balance < $totalToPay) {
                throw new \Exception("Saldo pada kartu kredit Anda tidak mencukupi.");
            }

            // Deduct balance from credit card
            $userCreditCard->current_balance -= $totalToPay;
            $userCreditCard->used_balance += $totalToPay;
            $userCreditCard->save();

            // Process payment on angsuran model
            $angsuran->processPayment();

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Pembayaran Berhasil!',
                'text' => 'Pembayaran angsuran telah berhasil diproses.',
            ]);

            $this->dispatch('angsuranPaid');

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('swal:error', [
                'title' => 'Pembayaran Gagal',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Angsuran::whereHas('pinjaman', function ($q) use ($user) {
            $q->where('user_id', $user->user_id);
        })->with(['pinjaman']);

        if ($this->pinjamanId) {
            $query->where('pinjaman_id', $this->pinjamanId);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('status', 'like', '%' . $this->search . '%')
                  ->orWhere('due_date', 'like', '%' . $this->search . '%')
                  ->orWhere('amount', 'like', '%' . $this->search . '%');
            });
        }

        $angsurans = $query->orderBy('due_date', 'asc')->paginate(10);

        return view('livewire.angsuran-management', [
            'angsurans' => $angsurans
        ]);
    }
}
