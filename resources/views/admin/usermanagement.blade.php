<x-layouts.app>
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">User Management</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage system users and access levels.</p>
        </div>
        <flux:button icon="plus" variant="primary">Add New User</flux:button>
    </div>

    {{-- Data Table --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Department</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($users as $user)
                <flux:table.row>
                    <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                        {{ $user->name }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $user->role === 'admin' ? 'red' : 'zinc' }}">{{ ucfirst($user->role) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->department }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button variant="ghost" size="sm" icon="pencil" />
                            <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    
    {{-- Pagination placeholder --}}
    <div>
        {{ $users->links() }}
    </div>
</div>

</x-layouts.app>