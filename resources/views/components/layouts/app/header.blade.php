<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col">
        <flux:header container class="border-b border-zinc-800 bg-zinc-900/90 backdrop-blur-xl">
            <flux:sidebar.toggle class="lg:hidden text-zinc-400 hover:text-white" icon="bars-2" inset="left" />

            @php
                $dashboardRoute = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard';
            @endphp

            <a href="{{ route($dashboardRoute) }}" class="ml-2 mr-5 flex items-center space-x-3 group lg:ml-0" wire:navigate>
                <div class="p-2 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 group-hover:bg-cyan-500/20 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm tracking-wide text-white block">{{ config('app.name') }}</span>
                    <span class="text-[10px] text-zinc-400 font-medium tracking-wider uppercase">Portal v1.0</span>
                </div>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" href="{{ route($dashboardRoute) }}" :current="request()->routeIs($dashboardRoute)" wire:navigate>
                    Dashboard
                </flux:navbar.item>

                @if(auth()->user()->role === 'admin')
                    <flux:navbar.item icon="ticket" href="{{ route('admin.open-tickets') }}" :current="request()->routeIs('admin.*')" wire:navigate>
                        Administration
                    </flux:navbar.item>
                @else
                    <flux:navbar.item icon="ticket" href="{{ route('user.tickets.index') }}" :current="request()->routeIs('user.tickets.*')" wire:navigate>
                        My Tickets
                    </flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="mr-1.5 space-x-0.5 py-0!">
                <flux:tooltip content="Repository" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5 text-zinc-400 hover:text-white"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        label="Repository"
                    />
                </flux:tooltip>
                <flux:tooltip content="Documentation" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5 text-zinc-400 hover:text-white"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits"
                        target="_blank"
                        label="Documentation"
                    />
                </flux:tooltip>
            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="end">
                <flux:profile
                    class="cursor-pointer text-zinc-300 hover:text-white"
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                >
                    <x-slot name="subtext">
                        {{ auth()->user()->department->name ?? ucfirst(auth()->user()->role) }}
                    </x-slot>
                </flux:profile>

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-semibold text-xs">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold text-zinc-200">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-zinc-400">{{ auth()->user()->department->name ?? ucfirst(auth()->user()->role) }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu Sidebar -->
        <flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-800 bg-zinc-900/90 backdrop-blur-xl">
            <flux:sidebar.toggle class="lg:hidden text-zinc-400 hover:text-white" icon="x-mark" />

            <a href="{{ route($dashboardRoute) }}" class="ml-1 flex items-center space-x-3 group" wire:navigate>
                <div class="p-2 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 group-hover:bg-cyan-500/20 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm tracking-wide text-white block">{{ config('app.name') }}</span>
                    <span class="text-[10px] text-zinc-400 font-medium tracking-wider uppercase">Portal v1.0</span>
                </div>
            </a>

            <flux:navlist variant="outline" class="space-y-6">
                <flux:navlist.group heading="Platform">
                    <flux:navlist.item icon="layout-grid" href="{{ route($dashboardRoute) }}" :current="request()->routeIs($dashboardRoute)" wire:navigate>
                        Dashboard
                    </flux:navlist.item>
                </flux:navlist.group>

                @if(auth()->user()->role === 'admin')
                    <flux:navlist.group heading="Ticket Management">
                        <flux:navlist.item icon="ticket" href="{{ route('admin.open-tickets') }}" :current="request()->routeIs('admin.open-tickets')" wire:navigate>
                            Opened Tickets
                        </flux:navlist.item>
                        <flux:navlist.item icon="clock" href="{{ route('admin.pending-tickets') }}" :current="request()->routeIs('admin.pending-tickets')" wire:navigate>
                            Pending Tickets
                        </flux:navlist.item>
                        <flux:navlist.item icon="check-circle" href="{{ route('admin.closed-tickets') }}" :current="request()->routeIs('admin.closed-tickets')" wire:navigate>
                            Closed Tickets
                        </flux:navlist.item>
                    </flux:navlist.group>
                @else
                    <flux:navlist.group heading="Support Hub">
                        <flux:navlist.item icon="ticket" href="{{ route('user.tickets.index') }}" :current="request()->routeIs('user.tickets.*')" wire:navigate>
                            My Tickets
                        </flux:navlist.item>
                        <flux:navlist.item icon="plus-circle" href="{{ route('user.tickets.create') }}" :current="request()->routeIs('user.tickets.create')" wire:navigate>
                            New Ticket
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    Repository
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                    Documentation
                </flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        <div class="flex-1 flex flex-col min-w-0 bg-zinc-950">
            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>