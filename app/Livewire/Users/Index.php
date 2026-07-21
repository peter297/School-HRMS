<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;


    public string $search = '';

    public bool $showEditModal = false;

    public string $userName = '';

    public string $userEmail = '';

    public string $userRole = '';


    public int $userId = 0;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openEditModal(int $userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userRole = $user->role;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function editUser(): void
    {

        $user = User::findOrFail($this->userId);


        $this->validate([
            'userName' => 'required|string|min:3',
            'userEmail' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'userRole' => 'required|in:staff_admin,hr_admin,super_admin,teacher',
        ]);

        $user->update([
            'name'  => $this->userName,
            'email' => $this->userEmail,
            'role'  => $this->userRole,
        ]);

        $this->showEditModal = false;
        session()->flash('success', 'User details have been updated successfully');
    }

    #[Layout('layouts.app')]
    public function render()
    {

        $users = User::query()
            ->when($this->search, fn($q) => $q->where(fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")))
            ->paginate(10);

        return view('livewire.users.index', compact('users'));
    }
}
