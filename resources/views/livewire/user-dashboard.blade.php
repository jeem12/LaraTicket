<?php

use Livewire\Component;
use App\Models\Ticket;
use Flux\Flux;

new class extends Component
{
    public function with(): array
    {
        $user = auth()->user();

        return [
            'totalTickets' => Ticket::where('user_id', $user->id)->count(),
            'openTickets' => Ticket::where('user_id', $user->id)->where('status', 'Open')->count(),
            'pendingTickets' => Ticket::where('user_id', $user->id)->where('status', 'pending')->count(),
            'closedTickets' => Ticket::where('user_id', $user->id)->where('status', 'Closed')->count(),
            'recentTickets' => Ticket::with(['messages' => function($query) {
                    $query->latest();
                }])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    {{-- Header Section --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">User Dashboard</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Overview of your submitted support tickets and recent thread activities.</p>
        </div>
    </div>

    {{-- Metric Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Tickets Card --}}
        <div class="p-5 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Total Submitted</p>
                <h3 class="text-2xl font-bold text-white">{{ $totalTickets }}</h3>
            </div>
            <div class="p-3 bg-zinc-800/60 rounded-xl text-zinc-300">
                <flux:icon name="ticket" class="w-6 h-6" />
            </div>
        </div>

        {{-- Open Tickets Card --}}
        <div class="p-5 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Open Tickets</p>
                <h3 class="text-2xl font-bold text-emerald-400">{{ $openTickets }}</h3>
            </div>
            <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-400 border border-emerald-500/20">
                <flux:icon name="check-circle" class="w-6 h-6" />
            </div>
        </div>

        {{-- Pending Tickets Card --}}
        <div class="p-5 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Pending Review</p>
                <h3 class="text-2xl font-bold text-amber-400">{{ $pendingTickets }}</h3>
            </div>
            <div class="p-3 bg-amber-500/10 rounded-xl text-amber-400 border border-amber-500/20">
                <flux:icon name="clock" class="w-6 h-6" />
            </div>
        </div>

        {{-- Closed Tickets Card --}}
        <div class="p-5 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Closed Tickets</p>
                <h3 class="text-2xl font-bold text-zinc-400">{{ $closedTickets }}</h3>
            </div>
            <div class="p-3 bg-zinc-800/60 rounded-xl text-zinc-400">
                <flux:icon name="archive-box" class="w-6 h-6" />
            </div>
        </div>
    </div>

    {{-- Recent Tickets & Replies Section --}}
    <div class="flex-1 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl flex flex-col space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-white">Recent Tickets & Replies</h2>
            <p class="text-xs text-zinc-400">A snapshot of your latest requests and status updates.</p>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Series / Subject</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Status</flux:table.column>
                <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Last Activity / Reply</flux:table.column>
                <flux:table.column align="end" class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Created</flux:table.column>
            </flux:table.columns>

            <flux:table.rows class="divide-y divide-zinc-800/60">
                @forelse($recentTickets as $ticket)
                    <flux:table.row class="hover:bg-zinc-800/40 transition-colors">
                        <flux:table.cell>
                            <div class="space-y-0.5">
                                @if(isset($ticket->ticket_number))
                                    <span class="font-mono text-[11px] font-semibold text-zinc-400 block">
                                        {{ $ticket->ticket_number }}
                                    </span>
                                @endif
                                <span class="font-medium text-zinc-200 block">
                                    {{ $ticket->subject ?? $ticket->title }}
                                </span>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            @php
                                $statusVariant = match(strtolower($ticket->status)) {
                                    'open' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    default => 'bg-zinc-800 text-zinc-300 border-zinc-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusVariant }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-xs text-zinc-300 italic">
                                @if($ticket->messages->isNotEmpty())
                                    "{{ Str::limit($ticket->messages->first()->message, 50) }}"
                                    <span class="block text-[10px] text-zinc-500 not-italic mt-0.5">
                                        {{ $ticket->messages->first()->created_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-zinc-500">No replies yet</span>
                                @endif
                            </span>
                        </flux:table.cell>
                        
                        <flux:table.cell align="end">
                            <span class="text-xs text-zinc-400">
                                {{ $ticket->created_at->format('Y-m-d H:i') }}
                            </span>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-zinc-500 py-12 text-sm">
                            You have not submitted any tickets yet.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>