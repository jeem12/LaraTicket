<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">My Support Tickets</h1>
                <p class="text-sm text-neutral-500">Track and manage your reported issues.</p>
            </div>
            <a href="{{ route('user.tickets.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + New Ticket
            </a>
        </div>

        <!-- Ticket History Table -->
        {{-- <div class="h-full flex-1 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-800 dark:text-neutral-100">Recent Activity</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                    <thead class="text-xs uppercase text-neutral-500">
                        <tr>
                            <th class="py-3 px-2">ID</th>
                            <th class="py-3 px-2">Subject</th>
                            <th class="py-3 px-2">Status</th>
                            <th class="py-3 px-2">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($userTickets as $ticket)
                            <tr class="border-t border-neutral-100 dark:border-neutral-700">
                                <td class="py-3 px-2 font-mono">#{{ substr($ticket->id, 0, 8) }}</td>
                                <td class="py-3 px-2 font-medium text-neutral-900 dark:text-neutral-100">{{ $ticket->subject }}</td>
                                <td class="py-3 px-2">
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-700 text-xs font-medium">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-2">{{ $ticket->updated_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-neutral-500">You haven't submitted any tickets yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> --}}

        <x-flux::table>
            <x-flux::table.columns>
                <x-flux::table.column>ID</x-flux::table.column>
                <x-flux::table.column>Subject</x-flux::table.column>
                <x-flux::table.column>Status</x-flux::table.column>
                <x-flux::table.column>Last Updated</x-flux::table.column>
            </x-flux::table.columns>

            @foreach($userTickets as $ticket)
                <x-flux::table.row>
                    <x-flux::table.cell>#{{ substr($ticket->id, 0, 8) }}</x-flux::table.cell>
                    <x-flux::table.cell>{{ $ticket->subject }}</x-flux::table.cell>
                    <x-flux::table.cell>
                        <x-flux::badge color="{{ $ticket->status === 'open' ? 'green' : 'gray' }}">
                            {{ ucfirst($ticket->status) }}
                        </x-flux::badge>
                    </x-flux::table.cell>
                    <x-flux::table.cell>{{ $ticket->updated_at->diffForHumans() }}</x-flux::table.cell>
                </x-flux::table.row>
            @endforeach
        </x-flux::table>

    </div>
</x-layouts.app>