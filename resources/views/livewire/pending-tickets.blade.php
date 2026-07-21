<?php

use Livewire\Component;
use App\Models\Ticket;
use Flux\Flux;

new class extends Component
{
    public function with(): array
    {
        return [
            'tickets' => Ticket::query()
                ->where('status', 'Pending')
                ->latest()
                ->get(),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Pending Tickets</h1>
            <p class="text-xs text-zinc-400 mt-0.5">List of support tickets currently awaiting action or review.</p>
        </div>
    </div>

    {{-- Data Table Container --}}
    <div class="flex-1 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl flex flex-col">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Title</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Priority</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Status</flux:table.column>
                <flux:table.column align="end" class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows class="divide-y divide-zinc-800/60">
                @forelse($tickets as $ticket)
                    <flux:table.row class="hover:bg-zinc-800/40 transition-colors">
                        <flux:table.cell>
                            <span class="font-medium text-zinc-200">
                                {{ $ticket->title }}
                            </span>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-zinc-800 text-zinc-300 border-zinc-700">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-amber-500/10 text-amber-400 border-amber-500/20">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </flux:table.cell>
                        
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button variant="ghost" size="sm" icon="eye" class="text-zinc-400 hover:text-white" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-zinc-500 py-12 text-sm">
                            No pending tickets found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>