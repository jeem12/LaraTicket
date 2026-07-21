<?php

use App\Models\Ticket;
use Livewire\Component;

new class extends Component
{
    /**
     * Pass computed data down to the Volt view template.
     */
    public function with(): array
    {
        $tickets = Ticket::with('assignedUser')->latest()->take(10)->get();

        return [
            'tickets' => $tickets,
            'totalCount' => Ticket::count(),
            'openCount' => Ticket::where('status', 'Open')->count(),
            'inProgressCount' => Ticket::where('status', 'In Progress')->count(),
            'resolvedCount' => Ticket::where('status', 'Resolved')->count(),
        ];
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        
    <!-- Dashboard Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Admin Dashboard</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Overview of system-wide IT support infrastructure and metrics.</p>
        </div>
        <flux:button variant="primary" icon="plus" href="{{ route('user.tickets.create') }}" wire:navigate class="bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-semibold border-0">
            Log New Issue
        </flux:button>
    </div>

    <!-- Metric Cards -->
    <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Total Tickets', $totalCount, 'folder-open', 'text-cyan-400', 'bg-cyan-500/10 border-cyan-500/20'], 
            ['Open', $openCount, 'ticket', 'text-amber-400', 'bg-amber-500/10 border-amber-500/20'], 
            ['In Progress', $inProgressCount, 'clock', 'text-blue-400', 'bg-blue-500/10 border-blue-500/20'], 
            ['Resolved', $resolvedCount, 'check-circle', 'text-emerald-400', 'bg-emerald-500/10 border-emerald-500/20']
        ] as [$title, $value, $icon, $textColor, $badgeClass])
            <div class="rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-5 flex items-center justify-between shadow-lg">
                <div>
                    <p class="text-xs font-medium text-zinc-400 uppercase tracking-wider">{{ $title }}</p>
                    <p class="text-3xl font-extrabold text-white mt-1">{{ $value }}</p>
                </div>
                <div class="p-3 rounded-xl border {{ $badgeClass }} flex items-center justify-center">
                    <flux:icon name="{{ $icon }}" class="w-6 h-6 {{ $textColor }}" />
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Tickets Table Area -->
    <div class="flex-1 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl flex flex-col">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-zinc-800/80">
            <h2 class="text-base font-bold text-white tracking-wide">Recent Tickets</h2>
            <span class="text-xs text-zinc-400">Showing last 10 requests</span>
        </div>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm text-zinc-300">
                <thead class="text-[11px] uppercase tracking-wider text-zinc-400 border-b border-zinc-800">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Ticket ID</th>
                        <th class="py-3 px-4 font-semibold">Subject</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 font-semibold">Assigned To</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-zinc-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-xs text-cyan-400 font-medium">#{{ substr($ticket->id, 0, 8) }}</td>
                            <td class="py-3.5 px-4 text-zinc-200 font-medium">{{ $ticket->subject }}</td>
                            <td class="py-3.5 px-4">
                                @php
                                    $statusClasses = match(strtolower($ticket->status)) {
                                        'open' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'in progress' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'resolved', 'closed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        default => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-zinc-400 text-xs font-medium">
                                {{ $ticket->assignedUser ? $ticket->assignedUser->name : 'Unassigned' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-zinc-500 text-sm">No tickets found in the database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
</div>