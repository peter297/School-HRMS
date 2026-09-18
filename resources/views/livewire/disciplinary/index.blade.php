<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Disciplinary</flux:heading>
            <flux:subheading>Manage disciplinary cases and letters</flux:subheading>
        </div>
        <flux:button href="{{ route('disciplinary.create') }}" icon="plus" variant="primary">
            New case
        </flux:button>
    </div>

    @if($openCount > 0)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
            <flux:callout.heading>{{ $openCount }} open disciplinary case(s)</flux:callout.heading>
            <flux:callout.text>These cases have not yet been closed.</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search case no. or employee…"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filterCategory">
                <flux:select.option value="">All categories</flux:select.option>
                <flux:select.option value="misconduct">Misconduct</flux:select.option>
                <flux:select.option value="insubordination">Insubordination</flux:select.option>
                <flux:select.option value="absenteeism">Absenteeism</flux:select.option>
                <flux:select.option value="performance">Poor performance</flux:select.option>
                <flux:select.option value="harassment">Harassment</flux:select.option>
                <flux:select.option value="theft">Theft</flux:select.option>
                <flux:select.option value="other">Other</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterStage">
                <flux:select.option value="">All stages</flux:select.option>
                <flux:select.option value="show_cause_issued">Show cause issued</flux:select.option>
                <flux:select.option value="response_received">Response received</flux:select.option>
                <flux:select.option value="hearing_held">Hearing held</flux:select.option>
                <flux:select.option value="outcome_recorded">Outcome recorded</flux:select.option>
                <flux:select.option value="warning_issued">Warning issued</flux:select.option>
                <flux:select.option value="closed">Closed</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterOutcome">
                <flux:select.option value="">All outcomes</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="verbal_warning">Verbal warning</flux:select.option>
                <flux:select.option value="written_warning">Written warning</flux:select.option>
                <flux:select.option value="final_written_warning">Final written warning</flux:select.option>
                <flux:select.option value="dismissal">Dismissal</flux:select.option>
                <flux:select.option value="no_action">No action</flux:select.option>
            </flux:select>
        </div>
    </flux:card>

    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Case no.</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Category</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Incident date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Stage</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Outcome</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($cases as $case)
                        <tr wire:key="{{ $case->id }}">
                            <td class="py-3 px-3 font-mono text-xs text-zinc-500">
                                {{ $case->case_number }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $case->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">
                                    {{ $case->employee->staff_number }}
                                </div>
                            </td>
                            <td class="py-3 px-3 text-zinc-500">{{ $case->category_label }}</td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $case->incident_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $case->stage_color }}" size="sm">
                                    {{ $case->stage_label }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $case->outcome_color }}" size="sm">
                                    {{ $case->outcome_label }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3">
                                <flux:button
                                    href="{{ route('disciplinary.show', $case) }}"
                                    size="sm" icon="eye" variant="ghost"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-zinc-400">
                                No disciplinary cases found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $cases->links() }}</div>
    </flux:card>
</div>