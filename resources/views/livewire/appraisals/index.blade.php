<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Appraisals</flux:heading>
            <flux:subheading>Manage staff performance appraisals</flux:subheading>
        </div>
        <flux:button href="{{ route('appraisals.create') }}" icon="plus" variant="primary">
            New appraisal
        </flux:button>
    </div>

    @if($pendingCount > 0)
        <flux:callout variant="warning" icon="clock" class="mb-4">
            <flux:callout.heading>{{ $pendingCount }} appraisal(s) awaiting HR approval</flux:callout.heading>
            <flux:callout.text>
                <button wire:click="$set('filterStatus', 'submitted')" class="underline">
                    View submitted
                </button>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search employee…"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filterType">
                <flux:select.option value="">All types</flux:select.option>
                <flux:select.option value="annual">Annual</flux:select.option>
                <flux:select.option value="mid_year">Mid-year</flux:select.option>
                <flux:select.option value="probation">Probation</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="submitted">Submitted</flux:select.option>
                <flux:select.option value="approved">Approved</flux:select.option>
                <flux:select.option value="rejected">Rejected</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterYear">
                @foreach(range(now()->year, now()->year - 4) as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </flux:card>

    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Type</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Period</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Score</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Grade</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Status</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($appraisals as $appraisal)
                        <tr wire:key="{{ $appraisal->id }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $appraisal->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">
                                    {{ $appraisal->employee->staff_number }} ·
                                    {{ $appraisal->employee->division_label }}
                                </div>
                            </td>
                            <td class="py-3 px-3 text-zinc-500">{{ $appraisal->type_label }}</td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $appraisal->period }}<br>
                                <span class="text-xs text-zinc-400">{{ $appraisal->year }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if($appraisal->overall_score)
                                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ number_format($appraisal->overall_score, 2) }}
                                    </span>
                                    <span class="text-xs text-zinc-400">/3.00</span>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($appraisal->overall_grade)
                                    <flux:badge color="{{ $appraisal->grade_color }}" size="sm">
                                        {{ $appraisal->grade_label }}
                                    </flux:badge>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $appraisal->status_color }}" size="sm">
                                    {{ ucfirst($appraisal->status) }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1">
                                    <flux:button
                                        href="{{ route('appraisals.show', $appraisal) }}"
                                        size="sm" icon="eye" variant="ghost"
                                    />
                                    @if($appraisal->status === 'approved')
                                        <flux:button
                                            href="{{ route('appraisals.pdf', $appraisal) }}"
                                            size="sm" icon="arrow-down-tray" variant="ghost"
                                        />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-zinc-400">
                                No appraisals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $appraisals->links() }}</div>
    </flux:card>
</div>