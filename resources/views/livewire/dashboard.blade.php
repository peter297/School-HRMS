

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>
                {{ today()->format('l, d F Y') }} · Al-Ameen Academy HRMS
            </flux:subheading>
        </div>
        <flux:button wire:click="$refresh" icon="arrow-path" variant="ghost" size="sm">
            Refresh
        </flux:button>
    </div>

    {{-- ── Alert banners ── --}}
    @if($expiringContracts > 0)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-3">
            <flux:callout.heading>{{ $expiringContracts }} contract(s) expiring soon</flux:callout.heading>
            <flux:callout.text>
                <a href="{{ route('contracts.index') }}" class="underline">Review contracts</a>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if($pendingLeaves > 0)
        <flux:callout variant="info" icon="clock" class="mb-3">
            <flux:callout.heading>{{ $pendingLeaves }} leave application(s) awaiting approval</flux:callout.heading>
            <flux:callout.text>
                <a href="{{ route('leaves.index') }}" class="underline">Review applications</a>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if($openIncidents > 0)
        <flux:callout variant="danger" icon="shield-exclamation" class="mb-4">
            <flux:callout.heading>{{ $openIncidents }} unresolved incident(s)</flux:callout.heading>
            <flux:callout.text>
                <a href="{{ route('time.incidents') }}" class="underline">Review incidents</a>
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- ── Top stat cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">Total staff</p>
                <flux:icon.users class="w-4 h-4 text-zinc-400"/>
            </div>
            <p class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $totalEmployees }}
            </p>
            <p class="text-xs text-zinc-400">
                {{ $activeEmployees }} active · {{ $onLeave }} on leave
            </p>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">Today's attendance</p>
                <flux:icon.clock class="w-4 h-4 text-zinc-400"/>
            </div>
            <p class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $todayPresent }}
            </p>
            <p class="text-xs text-zinc-400">
                {{ $todayLate }} late · {{ $todayAbsent }} absent · {{ $todayFlagged }} flagged
            </p>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">Open incidents</p>
                <flux:icon.exclamation-triangle class="w-4 h-4 text-zinc-400"/>
            </div>
            <p class="text-3xl font-semibold {{ $openIncidents > 0 ? 'text-red-500' : 'text-zinc-900 dark:text-zinc-100' }}">
                {{ $openIncidents }}
            </p>
            <p class="text-xs text-zinc-400">
                {{ $resolvedThisMonth }} resolved this month
            </p>
        </flux:card>

        <flux:card class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">Active contracts</p>
                <flux:icon.document-text class="w-4 h-4 text-zinc-400"/>
            </div>
            <p class="text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $activeContracts }}
            </p>
            <p class="text-xs text-zinc-400">
                {{ $expiringContracts }} expiring · {{ $expiredContracts }} expired
            </p>
        </flux:card>

    </div>

    {{-- ── Row 2: Staff breakdown + Today's attendance breakdown ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Staff by branch --}}
        <flux:card>
            <flux:heading size="sm" class="mb-4">Staff by branch</flux:heading>
            <div class="space-y-3">
                @foreach(['juja_road' => 'Juja Road', 'kitisuru' => 'Kitisuru', 'south_c' => 'South C'] as $key => $label)
                    @php $count = $byBranch[$key] ?? 0; $pct = $totalEmployees > 0 ? round(($count / $totalEmployees) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-zinc-600 dark:text-zinc-300">{{ $label }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-1.5">
                            <div class="bg-zinc-800 dark:bg-zinc-200 h-1.5 rounded-full transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <flux:separator class="my-4"/>

            <flux:heading size="sm" class="mb-4">Staff by type</flux:heading>
            <div class="space-y-2">
                @php
                    $typeLabels = [
                        'teacher'           => 'Teachers',
                        // 'teacher_upper_primary' => 'Teacher — Upper Primary',
                        // 'teacher_junior'        => 'Teacher — Junior School',
                        'admin'                 => 'Admin Staff',
                        'support_staff'         => 'Support Staff',
                    ];
                @endphp
                @foreach($typeLabels as $key => $label)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500">{{ $label }}</span>
                        <flux:badge size="sm" color="zinc">
                            {{ $byType[$key] ?? 0 }}
                        </flux:badge>
                    </div>
                @endforeach
            </div>
        </flux:card>

        {{-- Today's attendance breakdown --}}
        <flux:card>
            <flux:heading size="sm" class="mb-4">
                Today's attendance breakdown
                <span class="text-xs font-normal text-zinc-400 ml-2">
                    {{ today()->format('d M Y') }}
                </span>
            </flux:heading>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                    <p class="text-2xl font-semibold text-green-600">{{ $todayPresent }}</p>
                    <p class="text-xs text-green-500 mt-1">Present</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 text-center">
                    <p class="text-2xl font-semibold text-yellow-600">{{ $todayLate }}</p>
                    <p class="text-xs text-yellow-500 mt-1">Late</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center">
                    <p class="text-2xl font-semibold text-red-500">{{ $todayAbsent }}</p>
                    <p class="text-xs text-red-400 mt-1">Absent</p>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3 text-center">
                    <p class="text-2xl font-semibold text-zinc-600 dark:text-zinc-300">{{ $movementsThisMonth }}</p>
                    <p class="text-xs text-zinc-400 mt-1">Movements this month</p>
                </div>
            </div>

            {{-- Frequent movers --}}
            @if($frequentMovers->count() > 0)
                <flux:separator class="my-4"/>
                <flux:heading size="sm" class="mb-3">Top movers this month</flux:heading>
                <div class="space-y-2">
                    @foreach($frequentMovers as $mover)
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $mover->employee->full_name }}
                                </span>
                                <span class="text-xs text-zinc-400 ml-2">
                                    {{ $mover->employee->staff_number }}
                                </span>
                            </div>
                            <flux:badge
                                color="{{ $mover->total >= 5 ? 'red' : ($mover->total >= 3 ? 'yellow' : 'green') }}"
                                size="sm"
                            >
                                {{ $mover->total }}x
                            </flux:badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

    </div>

    {{-- ── Row 3: Recent incidents + Leaves this month ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Recent open incidents --}}
        <flux:card>
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="sm">Recent open incidents</flux:heading>
                <flux:button href="{{ route('time.incidents') }}" size="sm" variant="ghost" icon="arrow-right">
                    View all
                </flux:button>
            </div>

            @forelse($recentIncidents as $incident)
                <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $incident->employee->full_name }}
                        </p>
                        <p class="text-xs text-zinc-400 mt-0.5">
                            {{ $incident->date->format('d M Y') }} · {{ $incident->type_label }}
                        </p>
                        @if($incident->details)
                            <p class="text-xs text-zinc-400 mt-0.5">{{ $incident->details }}</p>
                        @endif
                    </div>
                    <flux:badge color="{{ $incident->type_color }}" size="sm" class="shrink-0 ml-3">
                        {{ $incident->type_label }}
                    </flux:badge>
                </div>
            @empty
                <div class="text-center py-8">
                    <flux:icon.check-circle class="w-8 h-8 text-green-400 mx-auto mb-2"/>
                    <p class="text-sm text-zinc-400">No open incidents</p>
                </div>
            @endforelse
        </flux:card>

        {{-- Leaves this month --}}
        <flux:card>
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="sm">
                    Approved leaves — {{ today()->format('F Y') }}
                </flux:heading>
                <flux:button href="{{ route('leaves.index') }}" size="sm" variant="ghost" icon="arrow-right">
                    View all
                </flux:button>
            </div>

            @forelse($leaveThisMonth as $leave)
                <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $leave->employee->full_name }}
                        </p>
                        <p class="text-xs text-zinc-400 mt-0.5">
                            {{ $leave->leaveType->name }} ·
                            {{ $leave->start_date->format('d M') }}
                            @if(!$leave->start_date->eq($leave->end_date))
                                – {{ $leave->end_date->format('d M') }}
                            @endif
                            · {{ $leave->duration_label }}
                        </p>
                    </div>
                    <flux:badge color="green" size="sm" class="shrink-0 ml-3">
                        Approved
                    </flux:badge>
                </div>
            @empty
                <div class="text-center py-8">
                    <flux:icon.calendar-days class="w-8 h-8 text-zinc-300 mx-auto mb-2"/>
                    <p class="text-sm text-zinc-400">No approved leaves this month</p>
                </div>
            @endforelse

            @if($pendingLeaves > 0)
                <flux:separator class="my-3"/>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-500">
                        <span class="font-medium text-yellow-600">{{ $pendingLeaves }}</span>
                        pending application(s)
                    </p>
                    <flux:button
                        href="{{ route('leaves.index') }}"
                        size="sm"
                        variant="primary"
                        icon="clock"
                    >
                        Review
                    </flux:button>
                </div>
            @endif
        </flux:card>

    </div>
</div>
