<x.app.layout>
<flux:modal name="create-user" class="md:w-96 space-y-6">
    <div class="space-y-1">
        <flux:heading size="lg">Add New User</flux:heading>
        <flux:subheading>Enter the details to create a new system user.</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-4">
        <flux:input wire:model="name" label="Name" placeholder="Full name" />
        
        <flux:input wire:model="email" label="Email" type="email" placeholder="email@example.com" />
        
        <flux:select wire:model="role" label="Role">
            <flux:select.option value="user">User</flux:select.option>
            <flux:select.option value="admin">Admin</flux:select.option>
        </flux:select>

        <flux:input wire:model="department" label="Department" placeholder="e.g. IT" />

        <div class="flex gap-2 justify-end">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary">Create User</flux:button>
        </div>
    </form>
</flux:modal>



</x.app.layout>