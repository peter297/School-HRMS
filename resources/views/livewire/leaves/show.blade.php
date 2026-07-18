

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('leaves.index') }}" icon="arrow-left" variant="ghost" size="sm" />
        <div>
            <flux:heading size="xl">Leave application</flux:heading>
            <flux:subheading>{{ $leave->employee->full_name }} · {{ $leave->leaveType->name }}</flux:subheading>
        </div>
    </div>

    <div class="max-w-2xl space-y-4">
        <flux:card>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-zinc-400">Employee</dt>
                    <dd class="font-medium mt-1">{{ $leave->employee->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">Staff number</dt>
                    <dd class="font-mono mt-1">{{ $leave->employee->staff_number }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">Leave type</dt>
                    <dd class="mt-1">{{ $leave->leaveType->name }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">Paid leave</dt>
                    <dd class="mt-1">{{ $leave->leaveType->is_paid ? 'Yes' : 'No' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">From</dt>
                    <dd class="mt-1">{{ $leave->start_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">To</dt>
                    <dd class="mt-1">{{ $leave->end_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">Working days</dt>
                    <dd class="mt-1">{{ $leave->duration_label }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-400">Status</dt>
                    <dd class="mt-1">
                        <flux:badge color="{{ $leave->status_color }}" size="sm">
                            {{ ucfirst($leave->status) }}
                        </flux:badge>
                    </dd>
                </div>
                @if($leave->reason)
                    <div class="col-span-2">
                        <dt class="text-zinc-400">Reason</dt>
                        <dd class="mt-1">{{ $leave->reason }}</dd>
                    </div>
                @endif
                @if($leave->rejection_reason)
                    <div class="col-span-2">
                        <dt class="text-zinc-400">Rejection reason</dt>
                        <dd class="mt-1 text-red-500">{{ $leave->rejection_reason }}</dd>
                    </div>
                @endif
                @if($leave->approvedBy)
                    <div>
                        <dt class="text-zinc-400">Actioned by</dt>
                        <dd class="mt-1">{{ $leave->approvedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Actioned at</dt>
                        <dd class="mt-1">{{ $leave->approved_at?->format('d M Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </flux:card>

        {{-- Balance card --}}
        @if($balance)
            <flux:card>
                <flux:heading size="sm" class="mb-3">Leave balance ({{ $leave->start_date->year }})</flux:heading>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100">
                            {{ $balance->entitled_days }}
                        </p>
                        <p class="text-xs text-zinc-400 mt-1">Entitled</p>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-red-500">{{ $balance->used_days }}</p>
                        <p class="text-xs text-zinc-400 mt-1">Used</p>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-green-500">{{ $balance->remaining_days }}</p>
                        <p class="text-xs text-zinc-400 mt-1">Remaining</p>
                    </div>
                </div>
            </flux:card>
        @endif
    </div>

    
</div>