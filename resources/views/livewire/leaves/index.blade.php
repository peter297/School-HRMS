<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Leave management</flux:heading>
            <flux:subheading>Review and manage staff leave applications</flux:subheading>
        </div>
        <flux:button href="{{ route('leaves.create') }}" icon="plus" variant="primary">
            New application
        </flux:button>
    </div>

    {{-- Pending alert --}}
    @if($pendingCount > 0)
        <flux:callout variant="warning" icon="clock" class="mb-4">
            <flux:callout.heading>{{ $pendingCount }} pending leave application(s)</flux:callout.heading>
            <flux:callout.text>
                These applications are awaiting your review.
                <button wire:click="$set('filterStatus', 'pending')" class="underline ml-1">Show pending</button>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif
    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-4">{{ session('error') }}</flux:callout>
    @endif

    {{-- Filters --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search employee…"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filterType" placeholder="All leave types">
                <flux:select.option value="">All types</flux:select.option>
                @foreach($leaveTypes as $type)
                    <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filterStatus" placeholder="All statuses">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="approved">Approved</flux:select.option>
                <flux:select.option value="rejected">Rejected</flux:select.option>
                <flux:select.option value="cancelled">Cancelled</flux:select.option>
            </flux:select>
            <flux:input wire:model.live="filterMonth" type="month" label="" />
        </div>
    </flux:card>

    {{-- Table --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Leave type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">From</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">To</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Days</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Actioned by</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse($leaves as $leave)
                        <tr wire:key="{{ $leave->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $leave->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">{{ $leave->employee->staff_number }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="{{ $leave->leaveType->is_paid ? 'text-green-600' : 'text-zinc-500' }} text-sm">
                                    {{ $leave->leaveType->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $leave->start_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $leave->end_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $leave->duration_label }}
                            </td>
                            <td class="px-4 py-3">
                                <flux:badge color="{{ $leave->status_color }}" size="sm">
                                    {{ ucfirst($leave->status) }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-zinc-500">
                                    {{ $leave->approvedBy?->name ?? '—' }}
                                    @if($leave->approved_at)
                                        <span class="text-xs block text-zinc-400">
                                            {{ $leave->approved_at->format('d M Y') }}
                                        </span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($leave->status === 'pending')
                                        <flux:button
                                            wire:click="approveLeave({{ $leave->id }})"
                                            size="sm" icon="check" variant="ghost"
                                            class="text-green-600"
                                        />
                                        <flux:button
                                            wire:click="openRejectModal({{ $leave->id }})"
                                            size="sm" icon="x-mark" variant="ghost"
                                            class="text-red-500"
                                        />
                                    @endif
                                    @if(in_array($leave->status, ['pending', 'approved']))
                                        <flux:button
                                            wire:click="cancelLeave({{ $leave->id }})"
                                            wire:confirm="Cancel this leave? Approved balance will be restored."
                                            size="sm" icon="arrow-uturn-left" variant="ghost"
                                        />
                                    @endif
                                    <flux:button
                                        href="{{ route('leaves.show', $leave) }}"
                                        size="sm" icon="eye" variant="ghost"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-zinc-400 text-sm">
                                No leave applications found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700">
            {{ $leaves->links() }}
        </div>
    </flux:card>

    {{-- Reject Modal --}}
    <flux:modal wire:model="showRejectModal" class="max-w-md">
        <flux:heading size="lg">Reject leave application</flux:heading>
        <flux:subheading>Please provide a reason for rejection.</flux:subheading>

        <div class="mt-4 space-y-4">
            <flux:textarea
                wire:model="rejectionReason"
                label="Rejection reason"
                placeholder="State why this leave is being rejected…"
                rows="3"
            />
            @error('rejectionReason')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showRejectModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="confirmReject" variant="danger" icon="x-mark">Reject leave</flux:button>
        </div>
    </flux:modal>
</div>
