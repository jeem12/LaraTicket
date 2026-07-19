<x-layouts.app>
    <div class="max-w-2xl p-6 mx-auto bg-white rounded-xl border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
        <h1 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Create New Ticket</h1>
        

        @if ($errors->any())
            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.tickets.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Subject</label>
                <input type="text" name="subject" required class="w-full mt-1 p-2 border rounded-lg dark:bg-neutral-900 dark:border-neutral-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Description</label>
                <textarea name="description" rows="5" required class="w-full mt-1 p-2 border rounded-lg dark:bg-neutral-900 dark:border-neutral-600"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                Submit Ticket
            </button>
        </form>
    </div>
</x-layouts.app>