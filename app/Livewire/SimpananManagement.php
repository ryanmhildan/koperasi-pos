<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Simpanan;
use App\Models\User;

class SimpananManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $simpananId;
    
    public $user_id, $amount, $transaction_date, $description;

    protected $rules = [
        'user_id' => 'required|exists:users,user_id',
        'amount' => 'required|numeric|min:0',
        'transaction_date' => 'required|date',
        'description' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->transaction_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Simpanan::create([
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Simpanan berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $simpanan = Simpanan::findOrFail($id);
        $this->simpananId = $id;
        $this->user_id = $simpanan->user_id;
        $this->amount = $simpanan->amount;
        $this->transaction_date = $simpanan->transaction_date->format('Y-m-d');
        $this->description = $simpanan->description;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $simpanan = Simpanan::findOrFail($this->simpananId);
        $simpanan->update([
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Simpanan berhasil diupdate.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Simpanan::find($id)->delete();
        session()->flash('message', 'Simpanan berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->user_id = '';
        $this->amount = '';
        $this->transaction_date = '';
        $this->description = '';
        $this->editMode = false;
        $this->simpananId = null;
    }

    public function render()
    {
        $simpanan = Simpanan::with('user')
            ->whereHas('user', function($query) {
                $query->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('nrp', 'like', '%'.$this->search.'%');
            })
            ->orWhere('description', 'like', '%'.$this->search.'%')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $users = User::role('Anggota')->get();

        return view('livewire.simpanan-management', compact('simpanan', 'users'));
    }
}