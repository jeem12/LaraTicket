<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Department;

class UserManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function edit($userId)
    {
        // Placeholder for edit action; implement as needed.
        $this->dispatchBrowserEvent('notification', [
            'type' => 'info',
            'message' => 'Edit action is not implemented yet.',
        ]);
    }

    public function delete($userId)
    {
        $user = User::find($userId);

        if ($user) {
            $user->delete();
            session()->flash('message', 'User deleted successfully.');
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::query()
                ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
                ->select('users.*', 'departments.name as department_name')
                ->latest()
                ->paginate(5),
            'departments' => Department::all(),
        ]);
    }
}
