<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use Illuminate\Database\Eloquent\Collection;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class AdminAngsuranManagement extends Component
{
    use WithPagination;

    public ?Pinjaman $pinjaman = null;
    public $search = '';
    public $refreshMe = false;

    public function mount($pinjamanId = null)
    {
        if ($pinjamanId) {
            $this->loadPinjaman($pinjamanId);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadPinjaman($pinjamanId)
    {
        $this->pinjaman = Pinjaman::with('user', 'angsuran')->findOrFail($pinjamanId);
    }
    
    public function requestMarkAsPaid($angsuranId)
    {
        $this->dispatch('openMarkAsPaidModal', angsuranId: $angsuranId);
    }

    #[On('installmentPaid')]
    public function refreshInstallmentData($pinjamanId)
    {
        if ($this->pinjaman && $this->pinjaman->pinjaman_id == $pinjamanId) {
            $this->loadPinjaman($pinjamanId);
            session()->flash('message', 'Data angsuran berhasil diperbarui.');
        } else {
            $this->refreshMe = !$this->refreshMe;
        }
    }
    
    #[On('installmentPaymentFailed')]
    public function render()
    {
        if ($this->pinjaman) {
            // Use the relationship to get angsuran, no need to query again in render
            $angsuran = $this->pinjaman->angsuran()->orderBy('due_date', 'asc')->get();
        } else {
            $angsuran = collect();
        }

        $pinjamanList = Pinjaman::with('user')
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%');
                })
                ->orWhere('pinjaman_id', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-angsuran-management', [
            'pinjamanList' => $pinjamanList,
            'angsuran' => $angsuran, // for the detail view
        ]);
    }
}
