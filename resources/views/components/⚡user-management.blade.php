<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $name, $email, $department, $password;
    public $editingId = null;
    public $showModal = false;

    public function create()
    {
        $this->reset(['name', 'email', 'department', 'password', 'editingId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department = $user->department;
        $this->showModal = true;
    }

    public function save()
    {
        $data = ['name' => $this->name, 'email' => $this->email, 'department' => $this->department];
        
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->editingId], $data);
        
        $this->showModal = false;
        $this->reset(['name', 'email', 'department', 'password', 'editingId']);
    }

    public function delete($id)
    {
        User::destroy($id);
    }

    public function with(): array
    {
        return [
            'users' => User::query()
                ->leftJoin('departments', 'departments.id', '=', DB::raw('CAST(users.department AS INTEGER)'))
                ->select('users.*', 'departments.name as department_name')
                ->latest()
                ->get(),
            'departments' => Department::all(),
        ];
    }
};
?>
        <div>
            
        {{-- Header Section --}}  
    <div class="flex justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">User Management</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage departments users and access levels.</p>
            </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Add New User</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Department</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($users as $user)
                <flux:table.row>
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="neutral">{{ $user->department_name ?? 'Unassigned' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $user->id }})" />
                            <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" 
                                wire:click="delete({{ $user->id }})" wire:confirm="Are you sure?" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal wire:model="showModal" :title="$editingId ? 'Edit User' : 'Add New User'">
        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="name" label="Name" required />
            <flux:input wire:model="email" label="Email" type="email" required />
            <flux:select wire:model="department" label="Department">
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>
            <flux:input wire:model="password" label="Password" type="password" :placeholder="$editingId ? 'Leave blank to keep current' : ''" />
            
            <div class="flex justify-end mt-4">
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Save' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>