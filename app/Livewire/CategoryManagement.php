<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class CategoryManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $categoryId;

    public $category_name, $description, $is_active = true;

    public $category_id_to_delete;
    public $confirmingCategoryDeletion = false;
    public $confirmingCategorySave = false;

    protected function rules()
    {
        return [
            'category_name' => 'required|unique:categories,category_name,' . $this->categoryId . ',category_id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', 'category-form-modal');
    }

    public function confirmCategorySave()
    {
        $this->validate();
        $this->confirmingCategorySave = true;
        $this->dispatch('open-modal', 'confirm-category-save');
    }

    public function store()
    {
        Category::create([
            'category_name' => $this->category_name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Kategori berhasil ditambahkan.');
        $this->closeModalAndReset();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->category_name = $category->category_name;
        $this->description = $category->description;
        $this->is_active = $category->is_active;
        $this->editMode = true;
        $this->dispatch('open-modal', 'category-form-modal');
    }

    public function update()
    {
        $category = Category::findOrFail($this->categoryId);
        $category->update([
            'category_name' => $this->category_name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Kategori berhasil diupdate.');
        $this->closeModalAndReset();
    }

    public function confirmCategoryDeletion($id)
    {
        $this->category_id_to_delete = $id;
        $this->confirmingCategoryDeletion = true;
        $this->dispatch('open-modal', 'confirm-category-deletion');
    }

    public function deleteCategory()
    {
        Category::find($this->category_id_to_delete)->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
        $this->confirmingCategoryDeletion = false;
        $this->dispatch('close-modal', 'confirm-category-deletion');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'category-form-modal');
        $this->resetInputFields();
    }

    private function closeModalAndReset()
    {
        $this->dispatch('close-modal', 'category-form-modal');
        $this->dispatch('close-modal', 'confirm-category-save');
        $this->resetInputFields();
        $this->confirmingCategorySave = false;
    }

    private function resetInputFields()
    {
        $this->categoryId = null;
        $this->category_name = '';
        $this->description = '';
        $this->is_active = true;
        $this->editMode = false;
    }

    public function render()
    {
        $categories = Category::paginate(10);
        return view('livewire.category-management', ['categories' => $categories]);
    }
}
