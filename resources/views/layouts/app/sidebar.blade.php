<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    <flux:sidebar sticky stashable class="bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <div class="px-4 py-5 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex flex-row justify-between h-10 w-10  rounded-lg bg-zinc-100 dark:bg-zinc-700 mt-2 mb-2">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Alameen Academy HRMS') }} Logo" class="h-10 w-auto" />

            </div>
             <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Alameen Academy HRMS</p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->role }}</p>
        </div>
    @if(auth()->check())
    @if(auth()->user()->isSuperAdmin())

        <flux:navlist class="px-2">

            <flux:navlist.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                Dashboard
            </flux:navlist.item>

            <flux:navlist.item icon="users" href="{{ route('employees.index') }}"
                :current="request()->routeIs('employees.*')">
                Employees
            </flux:navlist.item>

            <flux:navlist.item icon="document-text" href="{{ route('contracts.index') }}"
                :current="request()->routeIs('contracts.*')">
                Contracts
            </flux:navlist.item>

            <flux:navlist.item icon="calendar-days" href="{{ route('leaves.index') }}"
                :current="request()->routeIs('leaves.*')">
                Leave
            </flux:navlist.item>

            <flux:navlist.group heading="Time management" expandable>
                <flux:navlist.item icon="arrow-up-tray" href="{{ route('time.import') }}"
                    :current="request()->routeIs('time.import')">
                    Import attendance
                </flux:navlist.item>
                <flux:navlist.item icon="clock" href="{{ route('time.attendance') }}"
                    :current="request()->routeIs('time.attendance')">
                    Attendance
                </flux:navlist.item>
                <flux:navlist.item icon="exclamation-triangle" href="{{ route('time.incidents') }}"
                    :current="request()->routeIs('time.incidents')">
                    Lateness Incidents
                </flux:navlist.item>
                <flux:navlist.item icon="arrow-left-end-on-rectangle" href="{{ route('time.movements') }}"
                    :current="request()->routeIs('time.movements')">
                    Movements
                </flux:navlist.item>
            </flux:navlist.group>

        

            {{-- @if(auth()->user()->isSuperAdmin())
            <flux:navlist.item icon="cog-6-tooth" href="{{ route('users.index') }}"
                :current="request()->routeIs('users.*')">
                User management
            </flux:navlist.item>
            @endif --}}


        </flux:navlist>

        @elseif(auth()->user()->isTeacher())
        <flux:navlist class="px-2">

            <flux:navlist.item icon="home" href="{{ route('teacher.dashboard') }}" :current="request()->routeIs('teacher.dashboard')">
                Dashboard
            </flux:navlist.item>

        </flux:navlist>


        @endif  
        @endif

        {{-- <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode"  /> --}}


        <div class="mt-auto px-4 py-4 border-t border-zinc-200 dark:border-zinc-700">

            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-xs text-zinc-400 hover:text-red-500 mt-1">Sign out</button>
            </form>
        </div>
{{--
         <div>
                 <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="mx-auto">
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>
            </div> --}}

    </flux:sidebar>



    {{-- <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar> --}}

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                {{-- <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group> --}}
                {{-- <flux:radio.group x-data x-model="$flux.appearance">
                    <flux:radio value="light">Light</flux:radio>
                    <flux:radio value="dark">Dark</flux:radio>
                    <flux:radio value="system">System</flux:radio>
                </flux:radio.group> --}}
                {{-- <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
                    aria-label="Toggle dark mode" /> --}}
                    <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" />

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    <flux:toast position="top end" />

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist
    @fluxAppearance
    @fluxScripts
</body>

</html>
