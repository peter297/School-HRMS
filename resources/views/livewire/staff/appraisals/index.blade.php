<div>
    <div class="mb-6">
        <flux:heading size="xl">Appraisals</flux:heading>
        <flux:subheading>Your team's appraisals and your own performance reviews</flux:subheading>
    </div>

    {{-- My own appraisals --}}
    <flux:card class="mb-6">
        <flux:heading size="sm" class="mb-4">My appraisals</flux:heading>
        @forelse($myAppraisals as $a)
            <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ $a->type_label }} — {{ $a->period }}
                    </p>
                    @if($a->overall_grade)
                        <flux:badge color="{{ $a->grade_color }}" size="sm" class="mt-1">
                            {{ $a->grade_label }}
                        </flux:badge>
                    @endif
                </div>
                <flux:badge color="{{ $a->status_color }}" size="sm">
                    {{ ucfirst($a->status) }}
                </flux:badge>
            </div>
        @empty
            <p class="text-sm text-zinc-400">No appraisals on record.</p>
        @endforelse
    </flux:card>

    {{-- Team appraisals to fill --}}
    <flux:card>
        <flux:heading size="sm" class="mb-4">Team appraisals — pending your input</flux:heading>
        @forelse($appraisals as $appraisal)
            <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800 last:border-0"
                 wire:key="{{ $appraisal->id }}">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ $appraisal->employee->full_name }}
                    </p>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        {{ $appraisal->type_label }} · {{ $appraisal->period }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <flux:badge color="{{ $appraisal->status_color }}" size="sm">
                        {{ ucfirst($appraisal->status) }}
                    </flux:badge>
                    @if(in_array($appraisal->status, ['pending', 'rejected']))
                        <flux:button
                            href="{{ route('staff.appraisals.fill', $appraisal) }}"
                            size="sm" variant="primary" icon="pencil"
                        >
                            Fill in
                        </flux:button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-zinc-400">No appraisals assigned to you.</p>
        @endforelse
    </flux:card>
</div>
