<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100 flex">
        <flux:sidebar sticky stashable class="border-r border-zinc-800/80 bg-zinc-900/90 backdrop-blur-xl">
            <flux:sidebar.toggle class="lg:hidden text-zinc-400 hover:text-white" icon="x-mark" />

            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" 
            class="px-2 mb-2 flex items-center space-x-3 group" wire:navigate>
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
                <flux:navlist.group heading="Platform" class="grid gap-1">
                    @php
                        $dashboardRoute = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard';
                    @endphp
                    
                    <flux:navlist.item 
                        icon="home" 
                        :href="route($dashboardRoute)" 
                        :current="request()->routeIs($dashboardRoute)" 
                        wire:navigate
                    >
                        Dashboard
                    </flux:navlist.item>
                </flux:navlist.group>
                
                @if(auth()->user()->role === 'admin')
                    <flux:navlist.group heading="Ticket Management" class="grid gap-1">
                        <flux:navlist.item 
                            icon="ticket" 
                            href="{{ route('admin.open-tickets') }}" 
                            :current="request()->routeIs('admin.open-tickets')"
                            wire:navigate
                        >
                            Opened Tickets
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="clock" 
                            href="{{ route('admin.pending-tickets') }}" 
                            :current="request()->routeIs('admin.pending-tickets')"
                            wire:navigate
                        >
                            Pending Tickets
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="check-circle" 
                            href="{{ route('admin.closed-tickets') }}" 
                            :current="request()->routeIs('admin.closed-tickets')"
                            wire:navigate
                        >
                            Closed Tickets
                        </flux:navlist.item>
                    </flux:navlist.group>
                @else
                    <flux:navlist.group heading="Support Hub" class="grid gap-1">
                        <flux:navlist.item 
                            icon="ticket" 
                            href="{{ route('user.tickets.index') }}" 
                            :current="request()->routeIs('user.tickets.*')"
                            wire:navigate
                        >
                            My Tickets
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="plus-circle" 
                            href="{{ route('user.tickets.create') }}" 
                            :current="request()->routeIs('user.tickets.create')"
                            wire:navigate
                        >
                            New Ticket
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
                

                @if(auth()->user()->role === 'admin')
                    <flux:navlist.group heading="Administration" class="grid gap-1">
                        <flux:navlist.item 
                            icon="users" 
                            href="{{ route('admin.users.index') }}" 
                            :current="request()->routeIs('admin.users.*')"
                            wire:navigate
                        >
                            User Management
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="building-office" 
                            href="{{ route('admin.departments.index') }}" 
                            :current="request()->routeIs('admin.departments.*')"
                            wire:navigate
                        >
                            Department List
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="chart-bar" 
                            href="{{ route('admin.reports') }}" 
                            :current="request()->routeIs('admin.reports')"
                            wire:navigate
                        >
                            System Reports
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="cog-6-tooth" 
                            href="{{ route('admin.settings') }}" 
                            :current="request()->routeIs('admin.settings')"
                            wire:navigate
                        >
                            System Settings
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <div class="my-4 px-2">
                <div class="p-3 rounded-xl bg-zinc-900/50 border border-zinc-800/60 flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <div class="text-xs">
                        <span class="text-zinc-300 font-medium block">System Online</span>
                        <span class="text-zinc-500 text-[10px]">All services operational</span>
                    </div>
                </div>
            </div>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
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
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
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
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-zinc-900/80 backdrop-blur-md border-b border-zinc-800">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                >
                    <x-slot name="subtext">
                        {{ auth()->user()->department->name ?? ucfirst(auth()->user()->role) }}
                    </x-slot>
                </flux:profile>

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-semibold text-xs">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
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

        <div class="flex-1 flex flex-col min-w-0 bg-zinc-950">
            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>