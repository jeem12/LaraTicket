<?php

use Livewire\Component;
use App\Models\Department;

new class extends Component
{
    public $showModal = false;
    public $name = '';
    public $editingId = null;

    public function create()
    {
        $this->reset('name', 'editingId');
        $this->showModal = true;
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->editingId = $id;
        $this->name = $department->name;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:255']);

        if ($this->editingId) {
            // Update logic directly in the component
            Department::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            // Create logic directly in the component
            Department::create(['name' => $this->name]);
        }

        $this->reset('name', 'editingId', 'showModal');
    }

    public function delete($id)
    {
        Department::findOrFail($id)->delete();
    }

    public function with(): array
    {
        return [
        'departments' => Department::query()
            ->select('departments.*')
            // Manually add the subquery to count users
            ->selectRaw('(SELECT count(*) FROM users WHERE CAST(users.department AS integer) = departments.id) as users_count')
            ->get(),
        ];
    }
}; ?>

<div>


        <div class="flex justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Department Management</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage departments and their associated users.</p>
            </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Add New Department</flux:button>
    </div>

    

    {{-- The Modal must be inside the component to be reactive --}}
        <flux:modal wire:model="showModal" :title="$editingId ? 'Edit Department' : 'Add New Department'">
            <form wire:submit="save">
                <div class="space-y-4">
                    <flux:input wire:model="name" label="Name" placeholder="Enter department name" />
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary">
                            {{ $editingId ? 'Update' : 'Save' }}
                        </flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>

        {{-- Data Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Staff Count</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($departments as $department)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:icon.building-office class="text-zinc-400" />
                                <span class="font-medium text-zinc-900 dark:text-white">
                                    {{ $department->name }}
                                </span>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <flux:badge size="sm">{{ $department->users_count }} members</flux:badge>
                        </flux:table.cell>
                        
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                {{-- Edit Button --}}
                                <flux:button 
                                    variant="ghost" 
                                    size="sm" 
                                    icon="pencil" 
                                    wire:click="edit({{ $department->id }})" 
                                />

                                {{-- Delete Button --}}
                                <flux:button 
                                    variant="ghost" 
                                    size="sm" 
                                    icon="trash" 
                                    class="text-red-500 hover:text-red-700" 
                                    wire:click="delete({{ $department->id }})" 
                                    wire:confirm="Are you sure you want to delete this department?"
                                />
                            </div>
                        </flux:table.cell>
                        
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
</div>