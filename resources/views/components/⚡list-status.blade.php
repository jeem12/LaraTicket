<?php

use App\Models\Ticket;
use Livewire\Volt\Component;

new class extends Component {
    public string $status;

    public function mount(string $status): void
    {
        $this->status = $status;
    }

    public function with(): array
    {
        return [
            'tickets' => Ticket::where('status', $this->status)
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="xl" class="capitalize">{{ $status }} Tickets</flux:heading>
        <flux:badge size="sm" color="{{ $status === 'open' ? 'green' : ($status === 'pending' ? 'orange' : 'zinc') }}">
            {{ $tickets->total() }} Total
        </flux:badge>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Created</flux:table.column>
            <flux:table.column class="text-right">Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($tickets as $ticket)
                <flux:table.row :key="$ticket->id">
                    <flux:table.cell>{{ $ticket->id }}</flux:table.cell>
                    <flux:table.cell>{{ $ticket->title }}</flux:table.cell>
                    <flux:table.cell>{{ $ticket->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell class="text-right">
                        <flux:button variant="ghost" size="sm" icon="eye" href="#" wire:navigate>View</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</div>