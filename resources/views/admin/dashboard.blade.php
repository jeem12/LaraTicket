<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
        
        <!-- Dashboard Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">Admin Dashboard</h1>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Log New Issue
            </button>
        </div>

        <!-- Metric Cards -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
            {{-- In a real app, you would pass these counts from the Controller too --}}
            @foreach(['Total Tickets' => $tickets->count(), 'Open' => $tickets->where('status', 'Open')->count(), 'In Progress' => '0', 'Resolved' => '0'] as $title => $value)
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $title }}</p>
                    <p class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <!-- Recent Tickets Table Area -->
        <div class="h-full flex-1 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-800 dark:text-neutral-100">Recent Tickets</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                    <thead class="text-xs uppercase text-neutral-500">
                        <tr>
                            <th class="py-3 px-2">Ticket ID</th>
                            <th class="py-3 px-2">Subject</th>
                            <th class="py-3 px-2">Status</th>
                            <th class="py-3 px-2">Assigned To</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr class="border-t border-neutral-100 dark:border-neutral-700">
                                <td class="py-3 px-2 font-medium">#{{ substr($ticket->id, 0, 8) }}</td>
                                <td class="py-3 px-2">{{ $ticket->subject }}</td>
                                <td class="py-3 px-2">
                                    <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-yellow-800 text-xs">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-2">{{ $ticket->assignedUser ? $ticket->assignedUser->name : 'Unassigned' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-neutral-500">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</x-layouts.app>