<?php

use Livewire\Component;
use App\Models\Ticket;
use Flux\Flux;

new class extends Component
{
    public $search = '';

    public function closeTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        Flux::toast(
            variant: 'success',
            heading: 'Ticket Closed',
            text: "Ticket {$ticket->ticket_number} has been marked as closed."
        );
    }

    public function with(): array
    {
        $query = Ticket::with(['user', 'department'])
            ->where('status', 'Pending')
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('ticket_number', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'tickets' => $query->get(),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto">
    @persist('toast')
        <flux:toast />
    @endpersist

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Pending Tickets</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Review and manage incoming unassigned or pending support requests.</p>
        </div>
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by ticket # or subject..." icon="magnifying-glass" />
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/50 text-xs font-semibold uppercase tracking-wider text-zinc-400">
                        <th class="p-4">Ticket #</th>
                        <th class="p-4">Subject</th>
                        <th class="p-4">Requester</th>
                        <th class="p-4">Priority</th>
                        <th class="p-4">Category</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm text-zinc-300">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-zinc-800/40 transition-colors">
                            <td class="p-4 font-mono font-medium text-white">{{ $ticket->ticket_number }}</td>
                            <td class="p-4 font-medium text-white max-w-xs truncate">{{ $ticket->subject }}</td>
                            <td class="p-4 text-zinc-400">{{ $ticket->user->name ?? 'Unknown' }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($ticket->priority === 'urgent') bg-red-500/10 text-red-400 border border-red-500/20
                                    @elseif($ticket->priority === 'high') bg-orange-500/10 text-orange-400 border border-orange-500/20
                                    @elseif($ticket->priority === 'medium') bg-yellow-500/10 text-yellow-400 border border-yellow-500/20
                                    @else bg-zinc-800 text-zinc-400 @endif">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-800 text-zinc-300 border border-zinc-700">
                                    {{ ucfirst($ticket->category) }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <flux:button size="sm" variant="danger" wire:click="closeTicket({{ $ticket->id }})" icon="check-circle">
                                    Close
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-zinc-500">No pending tickets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>