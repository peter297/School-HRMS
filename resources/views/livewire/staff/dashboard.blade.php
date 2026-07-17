<div>
    <div class="mb-6">
        <flux:heading size="xl">
            Welcome, {{ $employee?->first_name ?? auth()->user()->name }}
        </flux:heading>
        <flux:subheading>{{ today()->format('l, d F Y') }}</flux:subheading>
    </div>

    @if($pendingApprovals > 0)
        <flux:callout variant="warning" icon="clock" class="mb-4">
            <flux:callout.heading>{{ $pendingApprovals }} leave application(s) awaiting your approval</flux:callout.heading>
            <flux:callout.text>
                <a href="{{ route('staff.approvals.index') }}" class="underline">Review now</a>
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- Leave balances --}}
    <div class="mb-6">
        <flux:heading size="sm" class="mb-3">My leave balances ({{ now()->year }})</flux:heading>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @forelse($balances as $balance)
                <flux:card class="text-center">
                    <p class="text-xs text-zinc-400 mb-1">{{ $balance->leaveType->name }}</p>
                    <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $balance->remaining_days }}
                    </p>
                    <p class="text-xs text-zinc-400 mt-1">
                        {{ $balance->used_days }} used of {{ $balance->entitled_days }}
                    </p>
                </flux:card>
            @empty
                <p class="text-sm text-zinc-400 col-span-4">
                    No leave balances set up yet. Contact HR.
                </p>
            @endforelse
        </div>
    </div>

    
    <flux:card>
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="sm">Recent applications</flux:heading>
            <flux:button href="{{ route('staff.leaves.create') }}" icon="plus" variant="primary" size="sm">
                Apply for leave
            </flux:button>
        </div>

        @forelse($myLeaves as $leave)
            <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ $leave->leaveType->name }}
                    </p>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        {{ $leave->start_date->format('d M Y') }}
                        @if(!$leave->start_date->eq($leave->end_date))
                            – {{ $leave->end_date->format('d M Y') }}
                        @endif
                        · {{ $leave->duration_label }}
                    </p>
                </div>
                <flux:badge color="{{ $leave->status_color }}" size="sm">
                    {{ $leave->approval_stage_label }}
                </flux:badge>
            </div>
        @empty
            <p class="text-sm text-zinc-400 text-center py-6">
                You have not applied for any leave yet.
            </p>
        @endforelse
    </flux:card>
</div>