<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Casuals & Interns</flux:heading>
            <flux:subheading>Manage casual workers and internship placements</flux:subheading>
        </div>
        <flux:button href="{{ route('casual-interns.create') }}" icon="plus" variant="primary">
            Add record
        </flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <flux:card class="text-center">
            <p class="text-sm text-zinc-500">Active casuals</p>
            <p class="text-3xl font-semibold text-blue-500 mt-1">{{ $totalCasuals }}</p>
        </flux:card>
        <flux:card class="text-center">
            <p class="text-sm text-zinc-500">Active interns</p>
            <p class="text-3xl font-semibold text-purple-500 mt-1">{{ $totalInterns }}</p>
        </flux:card>
    </div>

    {{-- Filters --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search name, staff no…"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filterType">
                <flux:select.option value="">All types</flux:select.option>
                <flux:select.option value="casual">Casual</flux:select.option>
                <flux:select.option value="intern">Intern</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="completed">Completed</flux:select.option>
                <flux:select.option value="terminated">Terminated</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterBranch">
                <flux:select.option value="">All branches</flux:select.option>
                <flux:select.option value="juja_road">Juja Road</flux:select.option>
                <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                <flux:select.option value="south_c">South C</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterDivision">
                <flux:select.option value="">All divisions</flux:select.option>
                <flux:select.option value="eye">EYE</flux:select.option>
                <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                <flux:select.option value="junior_school">Junior School</flux:select.option>
                <flux:select.option value="administration">Administration</flux:select.option>
                <flux:select.option value="support">Support</flux:select.option>
            </flux:select>
        </div>
    </flux:card>

    {{-- Table --}}
    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Name</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Type</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Division</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Branch</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Start date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">End date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Daily rate</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Status</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($records as $record)
                        <tr wire:key="{{ $record->id }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $record->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">{{ $record->staff_number }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $record->type_color }}" size="sm">
                                    {{ $record->type_label }}
                                </flux:badge>
                                @if($record->promoted)
                                    <flux:badge color="green" size="sm" class="ml-1">Promoted</flux:badge>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-zinc-500">{{ $record->division_label }}</td>
                            <td class="py-3 px-3 text-zinc-500">{{ $record->branch_label }}</td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $record->start_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $record->end_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $record->daily_rate
                                    ? 'KES '.number_format($record->daily_rate, 2)
                                    : '—' }}
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $record->status_color }}" size="sm">
                                    {{ ucfirst($record->status) }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1">
                                    <flux:button
                                        href="{{ route('casual-interns.show', $record) }}"
                                        size="sm" icon="eye" variant="ghost"
                                    />
                                    <flux:button
                                        href="{{ route('casual-interns.edit', $record) }}"
                                        size="sm" icon="pencil" variant="ghost"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-zinc-400">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    </flux:card>
</div>
