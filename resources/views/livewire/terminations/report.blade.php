
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Termination report</flux:heading>
            <flux:subheading>Analysis of staff terminations by reason, division and period</flux:subheading>
        </div>
        <flux:button href="{{ route('terminations.index') }}" icon="arrow-left" variant="ghost">
            Back to list
        </flux:button>
    </div>

    {{-- Filters --}}
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <flux:select wire:model.live="filterYear" label="Year">
                @foreach(range(now()->year, now()->year - 5) as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filterBranch" label="Branch">
                <flux:select.option value="">All branches</flux:select.option>
                <flux:select.option value="juja_road">Juja Road</flux:select.option>
                <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                <flux:select.option value="south_c">South C</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterDivision" label="Division">
                <flux:select.option value="">All divisions</flux:select.option>
                <flux:select.option value="eye">Early Years Education</flux:select.option>
                <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                <flux:select.option value="junior_school">Junior School</flux:select.option>
                <flux:select.option value="administration">Administration</flux:select.option>
                <flux:select.option value="support">Support</flux:select.option>
            </flux:select>
        </div>
    </flux:card>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <flux:card class="text-center">
            <p class="text-sm text-zinc-500">Total terminations</p>
            <p class="text-4xl font-semibold text-zinc-900 dark:text-zinc-100 mt-1">{{ $total }}</p>
        </flux:card>
        <flux:card class="text-center">
            <p class="text-sm text-zinc-500">Completed</p>
            <p class="text-4xl font-semibold text-green-500 mt-1">{{ $completed }}</p>
        </flux:card>
        <flux:card class="text-center">
            <p class="text-sm text-zinc-500">In progress</p>
            <p class="text-4xl font-semibold text-red-500 mt-1">{{ $inProgress }}</p>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- By reason --}}
        <flux:card>
            <flux:heading size="sm" class="mb-4">By reason</flux:heading>
            @forelse($byReason as $reason => $count)
                @php $pct = $total > 0 ? round(($count / $total) * 100) : 0; @endphp
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-zinc-600 dark:text-zinc-300">
                            {{ match($reason) {
                                'misconduct'   => 'Misconduct',
                                'redundancy'   => 'Redundancy',
                                'contract_end' => 'Contract end',
                                'performance'  => 'Performance',
                                'absenteeism'  => 'Absenteeism',
                                default        => 'Other',
                            } }}
                        </span>
                        <span class="font-medium">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-1.5">
                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-zinc-400">No data for selected period.</p>
            @endforelse
        </flux:card>

        {{-- By branch + division --}}
        <div class="space-y-4">
            <flux:card>
                <flux:heading size="sm" class="mb-4">By branch</flux:heading>
                @forelse($byBranch as $branch => $count)
                    <div class="flex justify-between text-sm py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                        <span class="text-zinc-600 dark:text-zinc-300">{{ $branch }}</span>
                        <flux:badge color="zinc" size="sm">{{ $count }}</flux:badge>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400">No data.</p>
                @endforelse
            </flux:card>
            <flux:card>
                <flux:heading size="sm" class="mb-4">By division</flux:heading>
                @forelse($byDivision as $division => $count)
                    <div class="flex justify-between text-sm py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                        <span class="text-zinc-600 dark:text-zinc-300">{{ $division }}</span>
                        <flux:badge color="zinc" size="sm">{{ $count }}</flux:badge>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400">No data.</p>
                @endforelse
            </flux:card>
        </div>
    </div>

    {{-- Monthly trend --}}
    <flux:card class="mb-6">
        <flux:heading size="sm" class="mb-4">Monthly trend</flux:heading>
        <div class="flex items-end gap-3 h-32">
            @php $maxMonth = $byMonth->max() ?: 1; @endphp
            @forelse($byMonth as $month => $count)
                <div class="flex flex-col items-center gap-1 flex-1">
                    <span class="text-xs text-zinc-400">{{ $count }}</span>
                    <div
                        class="w-full bg-red-400 rounded-t-sm"
                        style="height: {{ round(($count / $maxMonth) * 100) }}px; min-height: 4px"
                    ></div>
                    <span class="text-xs text-zinc-400 whitespace-nowrap">{{ $month }}</span>
                </div>
            @empty
                <p class="text-sm text-zinc-400">No data for selected period.</p>
            @endforelse
        </div>
    </flux:card>

    {{-- Full list --}}
    <flux:card>
        <flux:heading size="sm" class="mb-4">All terminations</flux:heading>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Division</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Branch</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Date</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Reason</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($terminations as $t)
                        <tr>
                            <td class="py-2 px-3">
                                <div class="font-medium">{{ $t->employee->full_name }}</div>
                                <div class="text-xs text-zinc-400">{{ $t->employee->staff_number }}</div>
                            </td>
                            <td class="py-2 px-3 text-zinc-500">{{ $t->employee->division_label }}</td>
                            <td class="py-2 px-3 text-zinc-500">{{ $t->employee->branch_label }}</td>
                            <td class="py-2 px-3 text-zinc-500">{{ $t->termination_date->format('d M Y') }}</td>
                            <td class="py-2 px-3">
                                <flux:badge color="{{ $t->reason_color }}" size="sm">
                                    {{ $t->reason_label }}
                                </flux:badge>
                            </td>
                            <td class="py-2 px-3">
                                <flux:badge color="{{ $t->status_color }}" size="sm">
                                    {{ $t->status_label }}
                                </flux:badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-zinc-400">No terminations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
</div>
