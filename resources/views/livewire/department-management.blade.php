<?php

use Livewire\Component;
use App\Models\Department;
use Flux\Flux;
use Illuminate\Validation\ValidationException;

new class extends Component
{
    public $showModal = false;
    public $showDeleteModal = false;
    public $name = '';
    public $editingId = null;
    public $deletingId = null;

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
        try {
            $this->validate(['name' => 'required|string|max:255']);

            if ($this->editingId) {
                Department::findOrFail($this->editingId)->update(['name' => $this->name]);
                $message = 'Department updated successfully.';
            } else {
                Department::create(['name' => $this->name]);
                $message = 'Department created successfully.';
            }

            $this->reset('name', 'editingId', 'showModal');

            Flux::toast(
                variant: 'success',
                heading: 'Success',
                text: $message
            );
        } catch (ValidationException $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Validation Error',
                text: 'Please check the input field for errors.'
            );

            throw $e;
        } catch (\Exception $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'An unexpected error occurred while saving.'
            );
        }
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $department = Department::findOrFail($this->deletingId);
            $deleted = $department->delete();

            $this->showDeleteModal = false;
            $this->deletingId = null;

            if ($deleted) {
                Flux::toast(
                    variant: 'success',
                    heading: 'Deleted',
                    text: 'Department deleted successfully.'
                );
            } else {
                Flux::toast(
                    variant: 'danger',
                    heading: 'Error',
                    text: 'Failed to delete department.'
                );
            }
        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Could not delete department due to active associations.'
            );
        }
    }

    public function with(): array
    {
        return [
            'departments' => Department::query()
                ->select('departments.*')
                ->selectRaw('(SELECT count(*) FROM users WHERE CAST(users.department AS integer) = departments.id) as users_count')
                ->get(),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    @persist('toast')
        <flux:toast />
    @endpersist

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Department Management</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Manage departments and their associated users.</p>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus" class="bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-semibold border-0">Add New Department</flux:button>
    </div>

    {{-- Static Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-modal" wire:model="showDeleteModal" :dismissible="false" class="space-y-6 md:w-96 bg-zinc-900 border border-zinc-800 text-white">
        <div>
            <flux:heading level="2" class="text-white">Delete Department</flux:heading>
            <flux:subheading class="text-zinc-400">Are you sure you want to delete this department? This action cannot be undone.</flux:subheading>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-800">
            <flux:modal.close>
                <flux:button variant="subtle" class="bg-zinc-800 text-zinc-300 hover:bg-zinc-700">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="delete" variant="danger" class="bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20">Yes, Delete</flux:button>
        </div>
    </flux:modal>

    {{-- Static Form Modal --}}
    <flux:modal wire:model="showModal" :dismissible="false" :title="$editingId ? 'Edit Department' : 'Add New Department'" class="space-y-6 md:w-96 bg-zinc-900 border border-zinc-800 text-white">
        <div>
            <flux:heading level="2" class="text-white">{{ $editingId ? 'Edit Department' : 'Add New Department' }}</flux:heading>
            <flux:subheading class="text-zinc-400">Enter the department details below.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="name" label="Name" placeholder="Enter department name" class="bg-zinc-950 border-zinc-800 text-white" />
            
            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="subtle" wire:click="$set('showModal', false)" class="bg-zinc-800 text-zinc-300 hover:bg-zinc-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-semibold border-0">
                    {{ $editingId ? 'Update Changes' : 'Save Department' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Data Table Container --}}
    <div class="flex-1 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl flex flex-col">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Department</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Staff Count</flux:table.column>
                <flux:table.column align="end" class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows class="divide-y divide-zinc-800/60">
                @foreach($departments as $department)
                    <flux:table.row class="hover:bg-zinc-800/40 transition-colors">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:icon.building-office class="text-cyan-400 w-5 h-5" />
                                <span class="font-medium text-zinc-200">
                                    {{ $department->name }}
                                </span>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-zinc-800 text-zinc-300 border-zinc-700">
                                {{ $department->users_count }} members
                            </span>
                        </flux:table.cell>
                        
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                {{-- Edit Button --}}
                                <flux:button 
                                    variant="ghost" 
                                    size="sm" 
                                    icon="pencil" 
                                    wire:click="edit({{ $department->id }})" 
                                    class="text-zinc-400 hover:text-white"
                                />

                                {{-- Delete Action Button triggering component state modal --}}
                                <flux:button 
                                    variant="ghost" 
                                    size="sm" 
                                    icon="trash" 
                                    class="text-red-400 hover:text-red-300" 
                                    wire:click="confirmDelete({{ $department->id }})" 
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>