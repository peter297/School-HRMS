<?php

namespace App\Livewire\Users;

use App\Models\Employees;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class LinkEmpolyees extends Component
{

    use WithPagination;

    public string $search = '';
    public bool $showUnlinked = false;

    // User Modal
    public bool $showCreateModal = false;
    public int $createEmployeeId = 0;

    public string $createEmail = '';

    public string $createPassword = '';

    public string $createRole = 'staff_admin';

    // Link Employees to Users
    public bool $showLinkModal = false;

    public int $linkEmployeeId = 0;

    public int $linkUserId = 0;

    public function updatingSearch(): void{
        $this->resetPage();
    }

    public function openCreateModal(int $employeeId): void{
        $employee = Employees::findOrFail($employeeId);
        $this->createEmployeeId = $employeeId;
        $this->createEmail = $employee->email ?? '';
        $this->createPassword = '';
        $this->createRole = 'staff_admin';
        $this->showCreateModal = true;
    }

    public function openLinkModal(int $employeeId): void{
        $this->linkEmployeeId = $employeeId;
        $this->linkUserId = 0;
        $this->showLinkModal = true;
    }

    public function createAndLink(): void{
        $this->validate([
            'createEmail' => 'required|email|unique:users,email',
            'createPassword' => 'required|min:8',
            'createRole' => 'required|in:staff_admin,hr_admin,super_admin,teacher',
        ]);

        $user = User::create([
            'name' => Employees::findOrFail($this->createEmployeeId)->full_name,
            'email' => $this->createEmail,
            'password' => Hash::make($this->createPassword),
            'role' => $this->createRole,
        ]);

        DB::table('employees')
            ->where('id', $this->createEmployeeId)
            ->update(['user_id' => $user->id]);

        $this->showCreateModal = false;
        session()->flash('success', 'User created and linked successfully. ');
    }

    // Link existing users
    public function linkToExisting(): void{
        $this->validate([
            'linkUserId' => 'required|exists:users,id',
        ]);

        DB::table('employees')
            ->where('user_id', $this->linkUserId)
            ->where('id', '!=', $this->linkEmployeeId)
            ->update(['user_id' => null]);

        DB::table('employees')
            ->where('id', $this->linkEmployeeId)
            ->update(['user_id' => $this->linkUserId]);

            $this->showLinkModal = false;
            session()->flash('success', 'Employee linked to existing user. ');
    }

    public function unlink(int $empolyeeId): void{
        DB::table('employees')
         ->where('id', $empolyeeId)
         ->update(['user_id' => null]);
         session()->flash('success', 'Employee unlinked from user.');

    }

    #[Layout('layouts.app')]
    public function render()
    {
        $employees = Employees::with('user')
            ->when($this->search, fn($q) => $q->where(fn($q) => $q->where('first_name', 'like', "%{$this->search}%")
             ->orWhere('last_name', 'like', "%{$this->search}%")
             ->orWhere('staff_number', 'like', "%{$this->search}%")
             ->orWhere('email', 'like', "%{$this->search}%")
             )
             )
             ->when($this->showUnlinked, fn($q) => $q->whereNull('user_id'))
             ->orderBy('first_name')
             ->paginate(20);

             $unlinkedCount = Employees::whereNull('user_id')->count();

             $availableUsers = User::whereNotIn('id', Employees::whereNotNull('user_id')->pluck('user_id'))
             ->orderBy('name')
             ->get();

        return view('livewire.users.link-empolyees', compact('employees', 'unlinkedCount', 'availableUsers'));
    }
}
