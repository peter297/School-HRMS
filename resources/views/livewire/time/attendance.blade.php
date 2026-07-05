

<div>
    <div class="mb-6">
        <flux:heading size="xl">Attendance</flux:heading>
        <flux:subheading>Daily attendance records generated from biometric imports</flux:subheading>
    </div>

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search employee…" icon="magnifying-glass"/>
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="present">Present</flux:select.option>
                <flux:select.option value="late">Late</flux:select.option>
                <flux:select.option value="absent">Absent</flux:select.option>
                <flux:select.option value="early_departure">Early departure</flux:select.option>
                <flux:select.option value="late_and_early">Late & early</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterBranch">
                <flux:select.option value="">All branches</flux:select.option>
                <flux:select.option value="juja_road">Juja Road</flux:select.option>
                <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                <flux:select.option value="south_c">South C</flux:select.option>
            </flux:select>
            <flux:input wire:model.live="filterDate" type="date"/>
        </div>
    </flux:card>

    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Branch</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Check in</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Check out</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Status</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Late (min)</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Early (min)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($records as $record)
                        <tr wire:key="{{ $record->id }}" class="{{ $record->flagged ? 'bg-red-50/40 dark:bg-red-900/10' : '' }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $record->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">{{ $record->employee->staff_number }}</div>
                            </td>
                            <td class="py-3 px-3 text-zinc-500">{{ $record->employee->branch_label }}</td>
                            <td class="py-3 px-3 text-zinc-600 dark:text-zinc-300">
                                {{ $record->date->format('d M Y') }}
                                <div class="text-xs text-zinc-400">{{ $record->date->format('l') }}</div>
                            </td>
                            <td class="py-3 px-3 font-mono text-sm {{ $record->minutes_late > 0 ? 'text-red-500' : 'text-zinc-600 dark:text-zinc-300' }}">
                                {{ $record->attendanceLog?->check_in ?? '—' }}
                            </td>
                            <td class="py-3 px-3 font-mono text-sm {{ $record->minutes_early > 0 ? 'text-yellow-600' : 'text-zinc-600 dark:text-zinc-300' }}">
                                {{ $record->attendanceLog?->check_out ?? '—' }}
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $record->status_color }}" size="sm">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3 text-center">
                                {{ $record->minutes_late > 0 ? $record->minutes_late : '—' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                {{ $record->minutes_early > 0 ? $record->minutes_early : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-zinc-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    </flux:card>
</div>
