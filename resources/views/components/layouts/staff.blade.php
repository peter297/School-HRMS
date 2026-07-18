
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Staff Portal</title>
    @include('partials.head')
    @fluxAppearance
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900">

<flux:sidebar sticky stashable class="bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700">

    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <div class="px-4 py-5 border-b border-zinc-200 dark:border-zinc-700">
        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Staff Portal</p>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">Al-Ameen Academy</p>
    </div>

    <flux:navlist class="mt-4 px-2">

        <flux:navlist.item icon="home" href="{{ route('staff.dashboard') }}"
            :current="request()->routeIs('staff.dashboard')">
            Home
        </flux:navlist.item>

        <flux:navlist.item icon="calendar-days" href="{{ route('staff.leaves.index') }}"
            :current="request()->routeIs('staff.leaves.*')">
            My leaves
        </flux:navlist.item>

        @if(auth()->user()->employee?->directReports()->exists())
            <flux:navlist.item icon="users" href="{{ route('staff.approvals.index') }}"
                :current="request()->routeIs('staff.approvals.*')">
                Team approvals
            </flux:navlist.item>
        @endif

    </flux:navlist>

    <div class="mt-auto px-4 py-4 border-t border-zinc-200 dark:border-zinc-700">
        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ auth()->user()->name }}
        </p>
        <p class="text-xs text-zinc-400 mt-0.5">
            {{ auth()->user()->employee?->staff_number }}
        </p>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button class="text-xs text-zinc-400 hover:text-red-500">Sign out</button>
        </form>
    </div>

     {{-- <div class="mt-auto px-4 py-4 border-t border-zinc-200 dark:border-zinc-700">

                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                <flux:text class="truncate">{{ auth()->user()->employee?->staff_number }}</flux:text>
                <flux:separator />

                <div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>

                </div>

    </div> --}}

</flux:sidebar>

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
                {{--
                <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
                    aria-label="Toggle dark mode" /> --}}
                <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
                    aria-label="Toggle dark mode" />

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

<flux:main class="p-6">
    {{ $slot }}
</flux:main>

@fluxScripts
</body>
</html>
