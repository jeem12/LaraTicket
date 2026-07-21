<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Department;

new class extends Component
{
    // Livewire will read this property to locate your layout file automatically
    protected $layout = 'components.layouts.app';

    public function with(): array
    {
        return [
            'users' => User::query()
                ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
                ->select('users.*', 'departments.name as department_name')
                ->latest()
                ->paginate(5),
            'departments' => Department::all(),
        ];
    }
};
?>

{{-- Single root HTML element (`div`) --}}
<div class="space-y-6">
    <flux:table :paginate="$users" :per-page="5">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Department</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
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
</div>
