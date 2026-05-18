

<div>
    <flux:heading size="xl">Dashboard</flux:heading>
    <flux:subheading>Welcome back, {{ auth()->user()->name }}</flux:subheading>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:card>
            <flux:heading size="sm">Total employees</flux:heading>
            <p class="text-3xl font-semibold mt-1">—</p>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">Pending leaves</flux:heading>
            <p class="text-3xl font-semibold mt-1">—</p>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">Active contracts</flux:heading>
            <p class="text-3xl font-semibold mt-1">—</p>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">Incidents today</flux:heading>
            <p class="text-3xl font-semibold mt-1">—</p>
        </flux:card>
    </div>
</div>
