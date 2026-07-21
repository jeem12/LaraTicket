<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Flux\Flux;

new class extends Component
{
    public ?string $prefix = null;
    public $first_name, $middle_name, $last_name;
    public ?string $suffix = null;
    public $email, $department, $role, $password;
    public $editingId = null;
    public $showModal = false;

    public function create()
    {
        $this->reset(['prefix', 'first_name', 'middle_name', 'last_name', 'suffix', 'email', 'department', 'role', 'password', 'editingId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->prefix = $user->prefix;
        $this->first_name = $user->first_name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->suffix = $user->suffix;
        $this->email = $user->email;
        $this->department = $user->department;
        $this->role = $user->role;
        $this->showModal = true;
    }

    public function save()
    {
        try {
            $validated = $this->validate([
                'prefix' => 'nullable|string|max:50',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'suffix' => 'nullable|string|max:50',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($this->editingId),
                ],
                'department' => 'required',
                'role' => 'required|in:admin,user',
                'password' => $this->editingId ? 'nullable|min:6' : 'required|min:6',
            ]);
        } catch (ValidationException $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Validation Error',
                text: 'Please check the required fields and fix the errors below.'
            );

            throw $e;
        }

        if (!empty($this->password)) {
            $validated['password'] = Hash::make($this->password);
        } else {
            unset($validated['password']);
        }

        $query = User::updateOrCreate(['id' => $this->editingId], $validated);
        
        $this->showModal = false;
        $this->reset([
            'prefix', 
            'first_name', 
            'middle_name', 
            'last_name', 
            'suffix', 
            'email', 
            'department', 
            'role', 
            'password', 
            'editingId'
        ]);

        if ($query){
            Flux::toast(
                variant: 'success',
                heading: 'Success',
                text: 'User saved successfully.'
            );
        } else {
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Failed to save user.'
            );
        }
    }

    public function delete($id)
    {
        $deleted = User::destroy($id);
        
        if($deleted){
            Flux::toast(
                variant: 'success',
                heading: 'Deleted',
                text: 'User deleted successfully.'
            );
        } else {
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Failed to delete user.'
            );
        }
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

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    @persist('toast')
        <flux:toast />
    @endpersist
        
    {{-- Header Section --}}  
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">User Management</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Manage departments users and access levels.</p>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus" class="bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-semibold border-0">Add New User</flux:button>
    </div>

    {{-- Data Table Container --}}
    <div class="flex-1 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl flex flex-col">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Fullname</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Email</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Department</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Role</flux:table.column>
                <flux:table.column align="end" class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows class="divide-y divide-zinc-800/60">
                @foreach($users as $user)
                    <flux:table.row class="hover:bg-zinc-800/40 transition-colors">
                        <flux:table.cell>
                            <span class="font-medium text-zinc-200">{{ $user->full_name }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-zinc-300 text-xs">{{ $user->email }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-zinc-800 text-zinc-300 border-zinc-700">
                                {{ $user->department_name ?? 'Unassigned' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border @if($user->role === 'admin') bg-cyan-500/10 text-cyan-400 border-cyan-500/20 @else bg-zinc-800/80 text-zinc-400 border-zinc-700 @endif">
                                {{ $user->role }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $user->id }})" class="text-zinc-400 hover:text-white" />
                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-400 hover:text-red-300" 
                                    wire:click="delete({{ $user->id }})" wire:confirm="Are you sure?" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- User Modal --}}
    <flux:modal wire:model="showModal" :dismissible="false" :title="$editingId ? 'Edit User' : 'Add New User'" class="bg-zinc-900 border border-zinc-800 text-white">
        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label class="text-zinc-300">First Name <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="first_name" required class="bg-zinc-950 border-zinc-800 text-white" />
            </flux:field>

            <flux:field>
                <flux:label class="text-zinc-300">Middle Name <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="middle_name" required class="bg-zinc-950 border-zinc-800 text-white" />
            </flux:field>

            <flux:field>
                <flux:label class="text-zinc-300">Last Name <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="last_name" required class="bg-zinc-950 border-zinc-800 text-white" />
            </flux:field>

            <flux:select wire:model="suffix" label="Suffix" class="bg-zinc-950 border-zinc-800 text-white">
                <option value=""></option>
                <option value="Jr">Jr.</option>
                <option value="Sr">Sr.</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
            </flux:select>
    
            <flux:select wire:model="prefix" label="Title" class="bg-zinc-950 border-zinc-800 text-white">
                <option value=""></option>
                <option value="Atty">Atty.</option>
                <option value="Engr">Engr.</option>
                <option value="Arch">Arch.</option>
                <option value="Dr">Dr.</option>
                <option value="Prof">Prof.</option>
            </flux:select>
            
            <flux:field>
                <flux:label class="text-zinc-300">Email <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="email" type="email" required class="bg-zinc-950 border-zinc-800 text-white" />
            </flux:field>

            <flux:field>
                <flux:label class="text-zinc-300">Password <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="password" type="password" :placeholder="$editingId ? 'Leave blank to keep current' : ''" class="bg-zinc-950 border-zinc-800 text-white" />
            </flux:field>

            <flux:field>
                <flux:label class="text-zinc-300">Department <span class="text-red-500">*</span></flux:label>
                <flux:select wire:model="department" class="bg-zinc-950 border-zinc-800 text-white">
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label class="text-zinc-300">Role <span class="text-red-500">*</span></flux:label>
                <flux:select wire:model="role" class="bg-zinc-950 border-zinc-800 text-white">
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </flux:select>
            </flux:field>
            
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="subtle" class="bg-zinc-800 text-zinc-300 hover:bg-zinc-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-semibold border-0">{{ $editingId ? 'Update' : 'Save' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>