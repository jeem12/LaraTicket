{{--
  File Name: opened-tickets.blade.php
  Description: 
  Developer: Unknown Developer
  Created Date: 2026-07-21
  Last Modified: 2026-07-21
--}}

<?php

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Flux\Flux;

new class extends Component
{
    public $search = '';
    public $selectedTicketId = null;
    public $replyMessage = '';

    public function selectTicket($id)
    {
        $this->selectedTicketId = $id;
        $this->replyMessage = '';
    }

    public function backToList()
    {
        $this->selectedTicketId = null;
        $this->replyMessage = '';
    }

    public function sendReply()
    {
        $this->validate([
            'replyMessage' => 'required|string|min:2',
        ]);

        $ticket = Ticket::findOrFail($this->selectedTicketId);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
        ]);

        $ticket->update([
            'last_replied_at' => now(),
        ]);

        $this->replyMessage = '';

        Flux::toast(
            variant: 'success',
            heading: 'Reply Sent',
            text: 'Your response has been added to the ticket thread.'
        );
    }

    public function closeTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update([
            'status' => 'Closed',
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
        $query = Ticket::with(['user', 'department', 'messages.user'])
            ->where('status', 'Opened')
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('ticket_number', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        $selectedTicket = $this->selectedTicketId 
            ? Ticket::with(['user', 'department', 'messages.user'])->find($this->selectedTicketId) 
            : null;

        return [
            'tickets' => $query->get(),
            'selectedTicket' => $selectedTicket,
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto">
    @persist('toast')
        <flux:toast />
    @endpersist

    @if(!$selectedTicket)
        {{-- Opened Tickets List View --}}
        <div class="flex flex-col gap-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Opened Support Tickets</h1>
                    <p class="text-xs text-zinc-400 mt-0.5">Manage active requests, monitor statuses, and review conversation threads.</p>
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
                                <th class="p-4">Status</th>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <flux:button size="sm" variant="ghost" wire:click="selectTicket({{ $ticket->id }})" icon="chat-bubble-left-right">
                                            View Thread
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="closeTicket({{ $ticket->id }})" icon="check-circle">
                                            Close
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-zinc-500">No open tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        {{-- Single Ticket Detail & Thread Reply View --}}
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <flux:button size="sm" variant="ghost" wire:click="backToList" icon="arrow-left">
                        Back to List
                    </flux:button>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-xl font-bold tracking-tight text-white">{{ $selectedTicket->subject }}</h1>
                            <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-zinc-800 text-zinc-300 border border-zinc-700">
                                {{ $selectedTicket->ticket_number }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-400 mt-1">
                            Opened by <span class="text-white">{{ $selectedTicket->user->name ?? 'System User' }}</span> on {{ $selectedTicket->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>
                <flux:button size="sm" variant="danger" wire:click="closeTicket({{ $selectedTicket->id }})" icon="check-circle">
                    Close Ticket
                </flux:button>
            </div>

            {{-- Main Ticket Description Card --}}
            <div class="rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-zinc-800 flex items-center justify-center font-bold text-white text-xs">
                            {{ substr($selectedTicket->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-white">{{ $selectedTicket->user->name ?? 'User' }}</span>
                            <span class="text-xs text-zinc-500 block">Original Description</span>
                        </div>
                    </div>
                    <span class="text-xs text-zinc-500">{{ $selectedTicket->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-zinc-300 whitespace-pre-wrap leading-relaxed">{{ $selectedTicket->description }}</p>
            </div>

            {{-- Conversation Thread / Messages --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider">Conversation Thread</h3>
                
                @foreach($selectedTicket->messages as $msg)
                    <div class="rounded-xl border border-zinc-800/60 bg-zinc-900/60 p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-800/60 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-7 w-7 rounded-full bg-zinc-800 flex items-center justify-center font-bold text-white text-xs">
                                    {{ substr($msg->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-white">{{ $msg->user->name ?? 'User' }}</span>
                            </div>
                            <span class="text-xs text-zinc-500">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-zinc-300 whitespace-pre-wrap leading-relaxed">{{ $msg->message }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Reply Form Box --}}
            <div class="rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl space-y-4">
                <h3 class="text-sm font-semibold text-white">Add Response</h3>
                <form wire:submit="sendReply" class="space-y-4">
                    <flux:textarea 
                        wire:model="replyMessage" 
                        rows="4" 
                        placeholder="Type your reply or status update here..." 
                        required 
                    />
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="paper-airplane">
                            Send Reply
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>