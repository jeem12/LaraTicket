<?php

use Livewire\Component;
use App\Models\Ticket;
use Flux\Flux;
use Illuminate\Support\Str;

new class extends Component
{
    public $subject = '';
    public $description = '';
    public $priority = 'medium';
    public $category = 'general';

    public function updatedSubject($value)
    {
        $this->analyzeAndAutoClassify($value . ' ' . $this->description);
    }

    public function updatedDescription($value)
    {
        $this->analyzeAndAutoClassify($this->subject . ' ' . $value);
    }

    protected function analyzeAndAutoClassify($text)
    {
        $lower = strtolower($text);

        if (str_contains($lower, 'urgent') || str_contains($lower, 'down') || str_contains($lower, 'crash') || str_contains($lower, 'emergency') || str_contains($lower, 'broken')) {
            $this->priority = 'urgent';
        } elseif (str_contains($lower, 'error') || str_contains($lower, 'fail') || str_contains($lower, 'issue') || str_contains($lower, 'unable')) {
            $this->priority = 'high';
        } elseif (str_contains($lower, 'slow') || str_contains($lower, 'question')) {
            $this->priority = 'medium';
        } else {
            $this->priority = 'low';
        }

        if (str_contains($lower, 'invoice') || str_contains($lower, 'billing') || str_contains($lower, 'payment') || str_contains($lower, 'charge') || str_contains($lower, 'subscription')) {
            $this->category = 'billing';
        } elseif (str_contains($lower, 'bug') || str_contains($lower, 'login') || str_contains($lower, 'error') || str_contains($lower, 'code') || str_contains($lower, 'api')) {
            $this->category = 'technical';
        } elseif (str_contains($lower, 'feature') || str_contains($lower, 'request') || str_contains($lower, 'suggest') || str_contains($lower, 'add')) {
            $this->category = 'feature';
        } else {
            $this->category = 'general';
        }
    }

    public function save()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:5',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string',
        ]);

        $user = auth()->user();

        // Generate the formatted sequential identifier matching your database schema (TCK-YYYY-000000)
        $year = date('Y');
        $latestTicket = Ticket::whereYear('created_at', $year)->latest('id')->first();
        $sequence = $latestTicket ? intval(substr($latestTicket->ticket_number, -6)) + 1 : 1;
        $ticketNumber = sprintf('TCK-%s-%06d', $year, $sequence);

        try {
            Ticket::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $user->id,
                'department_id' => $user->department_id ?? null,
                'subject' => $this->subject,
                'description' => $this->description,
                'status' => 'pending',
                'priority' => $this->priority,
                'category' => $this->category,
            ]);

            $this->reset(['subject', 'description', 'priority', 'category']);
            $this->priority = 'medium';
            $this->category = 'general';

            Flux::toast(
                variant: 'success',
                heading: 'Success',
                text: "Ticket {$ticketNumber} has been successfully created."
            );
        } catch (\Exception $e) {
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Failed to persist the ticket record to the database.'
            );
        }
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 max-w-4xl mx-auto">
    @persist('toast')
        <flux:toast />
    @endpersist

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Submit New Ticket</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Records map cleanly into your database columns with automated classification and user context mapping.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl p-8 shadow-xl">
        <form wire:submit="save" class="space-y-6">
            <flux:input 
                wire:model.live.debounce.300ms="subject" 
                label="Subject" 
                placeholder="Brief summary of your issue..." 
                required 
            />

            <flux:textarea 
                wire:model.live.debounce.500ms="description" 
                label="Description" 
                rows="6" 
                placeholder="Provide detailed information regarding your request..." 
                required 
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:select wire:model="priority" label="Priority (Auto)">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </flux:select>

                <flux:select wire:model="category" label="Category (Auto)">
                    <option value="general">General Inquiry</option>
                    <option value="technical">Technical Issue</option>
                    <option value="billing">Billing & Account</option>
                    <option value="feature">Feature Request</option>
                </flux:select>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="submit" variant="primary" icon="paper-airplane">
                    Save to Database
                </flux:button>
            </div>
        </form>
    </div>
</div>