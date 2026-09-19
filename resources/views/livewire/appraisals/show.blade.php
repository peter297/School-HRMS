

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('appraisals.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">
                {{ $appraisal->type_label }} — {{ $appraisal->employee->full_name }}
            </flux:heading>
            <flux:subheading>
                {{ $appraisal->period }} · {{ $appraisal->employee->staff_number }}
            </flux:subheading>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <flux:badge color="{{ $appraisal->status_color }}" size="sm">
                {{ ucfirst($appraisal->status) }}
            </flux:badge>
            @if($appraisal->status === 'approved')
                <flux:button
                    href="{{ route('appraisals.pdf', $appraisal) }}"
                    icon="arrow-down-tray" size="sm" variant="ghost"
                >
                    Download PDF
                </flux:button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KPI table --}}
        <div class="lg:col-span-2 space-y-4">
            <flux:card>
                <flux:heading size="sm" class="mb-4">KPI ratings</flux:heading>

                @if($appraisal->status === 'pending')
                    <flux:callout variant="info" icon="clock" class="mb-4">
                        <flux:callout.text>
                            Awaiting line manager to fill in ratings via their staff portal.
                        </flux:callout.text>
                    </flux:callout>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th class="text-left py-2 px-3 text-zinc-500 font-medium">#</th>
                                <th class="text-left py-2 px-3 text-zinc-500 font-medium">KPI</th>
                                <th class="text-left py-2 px-3 text-zinc-500 font-medium">Rating</th>
                                <th class="text-left py-2 px-3 text-zinc-500 font-medium">Score</th>
                                <th class="text-left py-2 px-3 text-zinc-500 font-medium">Comments</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($appraisal->kpis as $kpi)
                                <tr>
                                    <td class="py-2 px-3 text-zinc-400">{{ $loop->iteration }}</td>
                                    <td class="py-2 px-3 font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $kpi->kpi_label }}
                                    </td>
                                    <td class="py-2 px-3">
                                        @if($kpi->rating)
                                            <flux:badge color="{{ $kpi->rating_color }}" size="sm">
                                                {{ $kpi->rating_label }}
                                            </flux:badge>
                                        @else
                                            <span class="text-zinc-300 text-xs">Not rated</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-center font-semibold text-zinc-700 dark:text-zinc-300">
                                        {{ $kpi->score ?? '—' }}
                                    </td>
                                    <td class="py-2 px-3 text-zinc-500 text-xs max-w-xs">
                                        {{ $kpi->comments ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </flux:card>

            {{-- Comments --}}
            @if($appraisal->line_manager_comments || $appraisal->recommendations || $appraisal->hr_comments)
                <flux:card>
                    @if($appraisal->line_manager_comments)
                        <div class="mb-4">
                            <flux:heading size="sm" class="mb-2">Line manager comments</flux:heading>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $appraisal->line_manager_comments }}
                            </p>
                        </div>
                    @endif
                    @if($appraisal->recommendations)
                        <div class="mb-4">
                            <flux:heading size="sm" class="mb-2">Recommendations</flux:heading>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $appraisal->recommendations }}
                            </p>
                        </div>
                    @endif
                    @if($appraisal->hr_comments)
                        <div>
                            <flux:heading size="sm" class="mb-2">HR comments</flux:heading>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $appraisal->hr_comments }}
                            </p>
                        </div>
                    @endif
                </flux:card>
            @endif
        </div>

        {{-- Right panel --}}
        <div class="space-y-4">

            {{-- Score card --}}
            @if($appraisal->overall_score)
                <flux:card class="text-center">
                    <p class="text-xs text-zinc-400 mb-2">Overall score</p>
                    <p class="text-5xl font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($appraisal->overall_score, 2) }}
                    </p>
                    <p class="text-sm text-zinc-400 mt-1">out of 3.00</p>
                    @if($appraisal->overall_grade)
                        <div class="mt-3">
                            <flux:badge color="{{ $appraisal->grade_color }}">
                                {{ $appraisal->grade_label }}
                            </flux:badge>
                        </div>
                    @endif
                </flux:card>
            @endif

            {{-- Details --}}
            <flux:card>
                <flux:heading size="sm" class="mb-3">Details</flux:heading>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-400">Type</dt>
                        <dd>{{ $appraisal->type_label }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-400">Period</dt>
                        <dd>{{ $appraisal->period }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-400">Staff type</dt>
                        <dd>{{ $appraisal->employee->staff_type_label }}</dd>
                    </div>
                    @if($appraisal->submittedBy)
                        <div class="flex justify-between">
                            <dt class="text-zinc-400">Submitted by</dt>
                            <dd>{{ $appraisal->submittedBy->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-400">Submitted at</dt>
                            <dd>{{ $appraisal->submitted_at?->format('d M Y') }}</dd>
                        </div>
                    @endif
                    @if($appraisal->approvedBy)
                        <div class="flex justify-between">
                            <dt class="text-zinc-400">Approved by</dt>
                            <dd>{{ $appraisal->approvedBy->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-400">Approved at</dt>
                            <dd>{{ $appraisal->approved_at?->format('d M Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </flux:card>

            {{-- HR actions --}}
            @if($appraisal->status === 'submitted')
                <flux:card>
                    <flux:heading size="sm" class="mb-3">HR actions</flux:heading>
                    <div class="space-y-2">
                        <flux:button
                            wire:click="$set('showApproveModal', true)"
                            variant="primary" icon="check" class="w-full"
                        >
                            Approve appraisal
                        </flux:button>
                        <flux:button
                            wire:click="$set('showRejectModal', true)"
                            variant="ghost" icon="arrow-uturn-left" class="w-full"
                        >
                            Return for revision
                        </flux:button>
                    </div>
                </flux:card>
            @endif
        </div>
    </div>

    {{-- Approve modal --}}
    <flux:modal wire:model="showApproveModal" class="max-w-md">
        <flux:heading size="lg">Approve appraisal</flux:heading>
        <flux:subheading>
            The overall score will be calculated automatically from the KPI ratings.
        </flux:subheading>
        <div class="mt-4">
            <flux:textarea wire:model="hrComments" label="HR comments (optional)" rows="3"/>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showApproveModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="approve" variant="primary" icon="check">Approve</flux:button>
        </div>
    </flux:modal>

    {{-- Reject modal --}}
    <flux:modal wire:model="showRejectModal" class="max-w-md">
        <flux:heading size="lg">Return for revision</flux:heading>
        <flux:subheading>The line manager will need to revise and resubmit.</flux:subheading>
        <div class="mt-4">
            <flux:textarea wire:model="rejectionReason" label="Reason for return" rows="3" required/>
            @error('rejectionReason')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showRejectModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="reject" variant="ghost" icon="arrow-uturn-left">Return</flux:button>
        </div>
    </flux:modal>
</div>