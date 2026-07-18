<div>
    <div class="mb-6">
        <flux:heading size="xl">Team leave approvals</flux:heading>
        <flux:subheading>Leave applications from your direct reports</flux:subheading>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="mb-4">
        <flux:select wire:model.live="filterStage">
            <flux:select.option value="">All applications</flux:select.option>
            <flux:select.option value="pending_line_manager">Pending my approval</flux:select.option>
            <flux:select.option value="pending_hr">Forwarded to HR</flux:select.option>
            <flux:select.option value="approved">Approved</flux:select.option>
            <flux:select.option value="rejected_line_manager">Rejected by me</flux:select.option>
        </flux:select>
    </flux:card>

    <div class="space-y-4">
        @forelse($leaves as $leave)
            <flux:card wire:key="{{ $leave->id }}">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $leave->employee->full_name }}
                            </p>
                            <span class="text-xs text-zinc-400">{{ $leave->employee->staff_number }}</span>
                            <flux:badge color="{{ $leave->status_color }}" size="sm">
                                {{ $leave->approval_stage_label }}
                            </flux:badge>
                        </div>
                        <p class="text-sm text-zinc-500 mt-1">
                            {{ $leave->leaveType->name }} ·
                            {{ $leave->start_date->format('d M Y') }}
                            @if(!$leave->start_date->eq($leave->end_date))
                                – {{ $leave->end_date->format('d M Y') }}
                            @endif
                            · {{ $leave->duration_label }}
                        </p>
                        @if($leave->reason)
                            <p class="text-xs text-zinc-400 mt-1 italic">"{{ $leave->reason }}"</p>
                        @endif
                    </div>

                    @if($leave->approval_stage === 'pending_line_manager')
                        <div class="flex gap-2 shrink-0">
                            <flux:button
                                wire:click="openApproveModal({{ $leave->id }})"
                                size="sm" variant="primary" icon="check"
                            >
                                Approve
                            </flux:button>
                            <flux:button
                                wire:click="openRejectModal({{ $leave->id }})"
                                size="sm" variant="ghost" icon="x-mark"
                            >
                                Reject
                            </flux:button>
                        </div>
                    @endif
                </div>

                {{-- Approval trail --}}
                @if($leave->approvals->count() > 0)
                    <flux:separator class="my-3"/>
                    @foreach($leave->approvals as $approval)
                        <div class="flex items-start gap-3 text-xs">
                            <flux:badge color="{{ $approval->action_color }}" size="sm">
                                {{ ucfirst($approval->action) }}
                            </flux:badge>
                            <div class="text-zinc-500">
                                {{ $approval->stage_label }} · {{ $approval->actedBy->name }}
                                · {{ $approval->acted_at->format('d M Y H:i') }}
                                @if($approval->notes)
                                    <span class="block italic text-zinc-400 mt-0.5">"{{ $approval->notes }}"</span>
                                @endif
                                @if($approval->task_assigned_to)
                                    <span class="block text-zinc-400 mt-0.5">
                                        Tasks → {{ $approval->task_assigned_to }}
                                        @if($approval->task_description)
                                            : {{ $approval->task_description }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </flux:card>
        @empty
            <flux:card class="text-center py-12">
                <flux:icon.check-circle class="w-10 h-10 text-zinc-300 mx-auto mb-3"/>
                <p class="text-zinc-400">No applications found.</p>
            </flux:card>
        @endforelse

        <div>{{ $leaves->links() }}</div>
    </div>

    {{-- Approve modal --}}
    <flux:modal wire:model="showApproveModal" class="max-w-lg">
        <flux:heading size="lg">Approve leave</flux:heading>
        <flux:subheading>Assign tasks before forwarding to HR.</flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:input
                wire:model="taskAssignedTo"
                label="Tasks assigned to"
                placeholder="Name of person covering duties…"
                required
            />
            @error('taskAssignedTo')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <flux:textarea
                wire:model="taskDescription"
                label="Task details"
                placeholder="Describe what needs to be covered…"
                rows="3"
            />

            <flux:textarea
                wire:model="approveNotes"
                label="Notes (optional)"
                placeholder="Any additional comments…"
                rows="2"
            />
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showApproveModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="confirmApprove" variant="primary" icon="check">
                Approve & forward to HR
            </flux:button>
        </div>
    </flux:modal>

    {{-- Reject modal --}}
    <flux:modal wire:model="showRejectModal" class="max-w-md">
        <flux:heading size="lg">Reject leave</flux:heading>
        <flux:subheading>HR will be notified and may override this decision.</flux:subheading>

        <div class="mt-4">
            <flux:textarea
                wire:model="rejectNotes"
                label="Rejection reason"
                placeholder="Reason for rejection…"
                rows="3"
                required
            />
            @error('rejectNotes')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showRejectModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="confirmReject" variant="danger" icon="x-mark">Reject</flux:button>
        </div>
    </flux:modal>
</div>
