{{-- resources/views/livewire/staff/leaves/index.blade.php --}}

<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">My leaves</flux:heading>
            <flux:subheading>All your leave applications</flux:subheading>
        </div>
        <flux:button href="{{ route('staff.leaves.create') }}" icon="plus" variant="primary">
            Apply for leave
        </flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif
    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-4">{{ session('error') }}</flux:callout>
    @endif

    <div class="space-y-4">
        @forelse($leaves as $leave)
            <flux:card wire:key="{{ $leave->id }}">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $leave->leaveType->name }}
                            </p>
                            <flux:badge color="{{ $leave->status_color }}" size="sm">
                                {{ $leave->approval_stage_label }}
                            </flux:badge>
                        </div>
                        <p class="text-sm text-zinc-500 mt-1">
                            {{ $leave->start_date->format('d M Y') }}
                            @if(!$leave->start_date->eq($leave->end_date))
                                – {{ $leave->end_date->format('d M Y') }}
                            @endif
                            · {{ $leave->duration_label }}
                        </p>
                        @if($leave->reason)
                            <p class="text-xs text-zinc-400 mt-1">{{ $leave->reason }}</p>
                        @endif
                    </div>

                    @if(in_array($leave->approval_stage, ['pending_line_manager', 'pending_hr']))
                        <flux:button
                            wire:click="cancelLeave({{ $leave->id }})"
                            wire:confirm="Cancel this leave application?"
                            size="sm" variant="ghost" icon="x-mark"
                        >
                            Cancel
                        </flux:button>
                    @endif
                </div>

                {{-- Approval trail --}}
                @if($leave->approvals->count() > 0)
                    <flux:separator class="my-3"/>
                    <div class="space-y-2">
                        @foreach($leave->approvals as $approval)
                            <div class="flex items-start gap-3 text-xs">
                                <flux:badge color="{{ $approval->action_color }}" size="sm">
                                    {{ ucfirst($approval->action) }}
                                </flux:badge>
                                <div class="text-zinc-500">
                                    <span class="font-medium">{{ $approval->stage_label }}</span>
                                    · {{ $approval->actedBy->name }}
                                    · {{ $approval->acted_at->format('d M Y H:i') }}
                                    @if($approval->notes)
                                        <span class="block text-zinc-400 mt-0.5 italic">
                                            "{{ $approval->notes }}"
                                        </span>
                                    @endif
                                    @if($approval->task_assigned_to)
                                        <span class="block text-zinc-400 mt-0.5">
                                            Tasks assigned to: {{ $approval->task_assigned_to }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Rejection reason --}}
                @if($leave->rejection_reason)
                    <flux:separator class="my-3"/>
                    <p class="text-xs text-red-500">
                        Rejection reason: {{ $leave->rejection_reason }}
                    </p>
                @endif
            </flux:card>
        @empty
            <flux:card class="text-center py-12">
                <flux:icon.calendar-days class="w-10 h-10 text-zinc-300 mx-auto mb-3"/>
                <p class="text-zinc-400">No leave applications yet.</p>
                <flux:button href="{{ route('staff.leaves.create') }}" variant="primary" class="mt-4" icon="plus">
                    Apply for leave
                </flux:button>
            </flux:card>
        @endforelse

        <div>{{ $leaves->links() }}</div>
    </div>
</div>
