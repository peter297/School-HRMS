<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Resignations</flux:heading>
            <flux:subheading>Track and manage staff resignations</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('resignations.report') }}" icon="chart-bar" variant="ghost">
                Report
            </flux:button>
            <flux:button href="{{ route('resignations.create') }}" icon="plus" variant="primary">
                Record resignation
            </flux:button>
        </div>
    </div>

    @if($activeCount > 0)
        <flux:callout variant="warning" icon="clock" class="mb-4">
            <flux:callout.heading>{{ $activeCount }} active resignation(s) in progress</flux:callout.heading>
            <flux:callout.text>These have not yet reached the completed stage.</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search employee…"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="notice_served">Notice served</flux:select.option>
                <flux:select.option value="last_working_day">Last working day set</flux:select.option>
                <flux:select.option value="clearance_done">Clearance done</flux:select.option>
                <flux:select.option value="final_pay_processed">Final pay processed</flux:select.option>
                <flux:select.option value="completed">Completed</flux:select.option>
            </flux:select>
            <flux:input wire:model.live="filterMonth" type="month"/>
        </div>
    </flux:card>

    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Resignation date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Last working day</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Notice period</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Reason</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Progress</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Status</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($resignations as $resignation)
                        <tr wire:key="{{ $resignation->id }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $resignation->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">
                                    {{ $resignation->employee->staff_number }}
                                </div>
                            </td>
                            <td class="py-3 px-3 text-zinc-600 dark:text-zinc-300">
                                {{ $resignation->resignation_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3 text-zinc-600 dark:text-zinc-300">
                                {{ $resignation->last_working_day?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $resignation->notice_period_label }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $resignation->reason_label }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-zinc-100 dark:bg-zinc-700 rounded-full h-1.5">
                                        <div
                                            class="bg-green-500 h-1.5 rounded-full transition-all"
                                            style="width: {{ $resignation->completion_percentage }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-zinc-400 w-8">
                                        {{ $resignation->completion_percentage }}%
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $resignation->status_color }}" size="sm">
                                    {{ $resignation->status_label }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3">
                                <flux:button
                                    href="{{ route('resignations.show', $resignation) }}"
                                    size="sm" icon="eye" variant="ghost"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-zinc-400">
                                No resignations recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $resignations->links() }}</div>
    </flux:card>
</div>
