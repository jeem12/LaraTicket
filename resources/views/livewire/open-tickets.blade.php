<?php

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Flux\Flux;

new class extends Component
{
    public $activeTicketId = null;
    public $replyMessage = '';

    public function viewThread($id)
    {
        $this->activeTicketId = $id;
        $this->reset('replyMessage');
    }

    public function backToTable()
    {
        $this->activeTicketId = null;
        $this->reset('replyMessage');
    }

    public function postReply()
    {
        $this->validate([
            'replyMessage' => 'required|string|min:2',
        ]);

        try {
            TicketMessage::create([
                'ticket_id' => $this->activeTicketId,
                'user_id' => auth()->id(),
                'message' => $this->replyMessage,
            ]);

            Ticket::where('id', $this->activeTicketId)->update([
                'last_replied_at' => now(),
                'status' => 'Pending',
            ]);

            $this->reset('replyMessage');

            Flux::toast(
                variant: 'success',
                heading: 'Success',
                text: 'Reply posted successfully.'
            );
        } catch (\Exception $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Failed to post reply.'
            );
        }
    }

    public function closeTicket($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->update([
                'status' => 'Closed',
                'resolved_at' => now(),
            ]);

            if ($this->activeTicketId === $id) {
                $this->activeTicketId = null;
            }

            Flux::toast(
                variant: 'success',
                heading: 'Success',
                text: 'Ticket has been closed successfully.'
            );
        } catch (\Exception $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Failed to close the ticket.'
            );
        }
    }

    public function with(): array
    {
        return [
            'tickets' => Ticket::query()
                ->where('status', 'Open')
                ->latest()
                ->get(),
            'currentTicket' => $this->activeTicketId 
                ? Ticket::with(['user', 'messages.user', 'assignedUser', 'department'])->find($this->activeTicketId) 
                : null,
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    @persist('toast')
        <flux:toast />
    @endpersist

    @if($activeTicketId && $currentTicket)
        {{-- Thread / Detail View --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:button variant="ghost" icon="arrow-left" wire:click="backToTable" class="text-zinc-300 hover:text-white">Back to Tickets</flux:button>
                <flux:button variant="danger" size="sm" icon="check-circle" wire:click="closeTicket({{ $currentTicket->id }})" wire:confirm="Are you sure you want to close this ticket?" class="bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20">Close Ticket</flux:button>
            </div>

            <div class="p-6 bg-zinc-900/90 backdrop-blur-xl rounded-2xl border border-zinc-800/80 shadow-xl space-y-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-emerald-500/10 text-emerald-400 border-emerald-500/20">
                            {{ ucfirst($currentTicket->status) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-zinc-800 text-zinc-300 border-zinc-700">
                            {{ $currentTicket->ticket_number }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mt-3">{{ $currentTicket->subject }}</h2>
                    <p class="text-xs text-zinc-400 mt-1">Submitted by <span class="font-medium text-zinc-200">{{ $currentTicket->user->name ?? 'Unknown' }}</span> on {{ $currentTicket->created_at->format('Y-m-d H:i') }}</p>
                </div>

                <div class="p-4 bg-zinc-950/60 rounded-xl border border-zinc-800/60 text-zinc-300">
                    <p class="whitespace-pre-line text-sm leading-relaxed">{{ $currentTicket->description }}</p>
                </div>
            </div>

            {{-- Thread Conversation / Replies Section --}}
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white tracking-wide">Conversation Thread</h3>
                
                @forelse($currentTicket->messages as $msg)
                    <div class="p-5 bg-zinc-900/90 backdrop-blur-xl rounded-2xl border border-zinc-800/80 shadow-xl space-y-2">
                        <div class="flex justify-between items-center text-xs text-zinc-400">
                            <span class="font-semibold text-white">{{ $msg->user->name ?? 'User' }}</span>
                            <span>{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="text-sm text-zinc-300 whitespace-pre-line leading-relaxed">{{ $msg->message }}</p>
                    </div>
                @empty
                    <div class="p-6 bg-zinc-900/90 backdrop-blur-xl rounded-2xl border border-zinc-800/80 text-center text-zinc-400 text-sm shadow-xl">
                        No replies yet. Start the conversation below.
                    </div>
                @endforelse
            </div>

            {{-- Reply Form with Textarea --}}
            <div class="p-6 bg-zinc-900/90 backdrop-blur-xl rounded-2xl border border-zinc-800/80 shadow-xl space-y-4">
                <h3 class="text-lg font-bold text-white tracking-wide">Reply to Ticket</h3>
                
                <form wire:submit="postReply" class="space-y-4">
                    <flux:textarea wire:model="replyMessage" rows="4" placeholder="Type your response here..." label="Message" class="bg-zinc-950 border-zinc-800 text-white" />
                    
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="paper-airplane" class="bg-cyan-500 hover:bg-cyan-400 text-zinc-950 font-semibold border-0">Send Reply</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- List View --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Open Tickets</h1>
                <p class="text-xs text-zinc-400 mt-0.5">List of currently active and open support tickets.</p>
            </div>
        </div>

        <div class="flex-1 rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-6 shadow-xl flex flex-col">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Series</flux:table.column>
                    <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Subject</flux:table.column>
                    <flux:table.column class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Status</flux:table.column>
                    <flux:table.column align="end" class="text-zinc-400 font-semibold text-[11px] uppercase tracking-wider">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows class="divide-y divide-zinc-800/60">
                    @forelse($tickets as $ticket)
                        <flux:table.row class="hover:bg-zinc-800/40 transition-colors">
                            <flux:table.cell>
                                <span class="font-mono text-xs font-semibold text-cyan-400">
                                    {{ $ticket->ticket_number }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-medium text-zinc-200">
                                    {{ $ticket->subject }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-emerald-500/10 text-emerald-400 border-emerald-500/20">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </flux:table.cell>
                            
                            <flux:table.cell align="end">
                                <div class="flex justify-end gap-2">
                                    {{-- View Thread Button --}}
                                    <flux:button 
                                        variant="ghost" 
                                        size="sm" 
                                        icon="eye" 
                                        wire:click="viewThread({{ $ticket->id }})" 
                                        class="text-zinc-400 hover:text-white"
                                    />

                                    {{-- Close Ticket Button --}}
                                    <flux:button 
                                        variant="ghost" 
                                        size="sm" 
                                        icon="check-circle" 
                                        class="text-emerald-400 hover:text-emerald-300" 
                                        wire:click="closeTicket({{ $ticket->id }})" 
                                        wire:confirm="Are you sure you want to close this ticket?"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center text-zinc-500 py-12 text-sm">
                                No open tickets found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @endif
</div>