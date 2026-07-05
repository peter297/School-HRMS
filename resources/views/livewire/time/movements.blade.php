

<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">1–2 PM movement tracker</flux:heading>
            <flux:subheading>HR-recorded permitted exits during the allowed window</flux:subheading>
        </div>
        <flux:button wire:click="openAddModal" icon="plus" variant="primary">
            Record movement
        </flux:button>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    {{-- Filters --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search employee…" icon="magnifying-glass"/>
            <flux:select wire:model.live="filterBranch">
                <flux:select.option value="">All branches</flux:select.option>
                <flux:select.option value="juja_road">Juja Road</flux:select.option>
                <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                <flux:select.option value="south_c">South C</flux:select.option>
            </flux:select>
            <flux:input wire:model.live="filterMonth" type="month"/>
        </div>
    </flux:card>

    {{-- Monthly summary cards --}}
    @if($summary->count() > 0)
        <div class="mb-6">
            <flux:heading size="sm" class="mb-3">Monthly summary</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($summary as $row)
                    <flux:card>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100 text-sm">
                                    {{ $row['employee']->full_name }}
                                </p>
                                <p class="text-xs text-zinc-400">{{ $row['employee']->staff_number }}</p>
                            </div>
                            <flux:badge color="{{ $row['total_movements'] >= 5 ? 'red' : ($row['total_movements'] >= 3 ? 'yellow' : 'green') }}" size="sm">
                                {{ $row['total_movements'] }}x
                            </flux:badge>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-zinc-500">
                            <div>
                                <span class="block text-zinc-400">Avg duration</span>
                                <span class="font-medium">
                                    {{ $row['avg_duration'] ? round($row['avg_duration']) . ' min' : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-zinc-400">Days out</span>
                                <span class="font-medium">{{ $row['days']->implode(', ') }}</span>
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Detailed log table --}}
    <flux:card>
        <flux:heading size="sm" class="mb-4">Movement log</flux:heading>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Branch</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Exit</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Return</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Duration</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Reason</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Recorded by</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($exits as $exit)
                        <tr wire:key="{{ $exit->id }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $exit->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">{{ $exit->employee->staff_number }}</div>
                            </td>
                            <td class="py-3 px-3 text-zinc-500">{{ $exit->employee->branch_label }}</td>
                            <td class="py-3 px-3 text-zinc-600 dark:text-zinc-300">
                                {{ $exit->date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3 font-mono text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $exit->exit_time }}
                            </td>
                            <td class="py-3 px-3 font-mono text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $exit->return_time ?? '—' }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $exit->duration_minutes !== null ? $exit->duration_minutes . ' min' : '—' }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500 text-xs max-w-[160px]">
                                {{ $exit->reason ?? '—' }}
                            </td>
                            <td class="py-3 px-3 text-xs text-zinc-500">
                                {{ $exit->recordedBy->name }}
                            </td>
                            <td class="py-3 px-3">
                                <flux:button
                                    wire:click="deleteMovement({{ $exit->id }})"
                                    wire:confirm="Delete this movement record?"
                                    size="sm" icon="trash" variant="ghost"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-zinc-400">
                                No movements recorded for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $exits->links() }}</div>
    </flux:card>

    {{-- Add movement modal --}}
    <flux:modal wire:model="showAddModal" class="max-w-lg">
        <flux:heading size="lg">Record 1–2 PM movement</flux:heading>
        <flux:subheading>Log a permitted exit during the allowed window.</flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:select wire:model="employee_id" label="Employee" required>
                <flux:select.option value="">Select employee…</flux:select.option>
                @foreach($employees as $emp)
                    <flux:select.option value="{{ $emp->id }}">
                        {{ $emp->full_name }} ({{ $emp->staff_number }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="date" type="date" label="Date" required/>

            <div class="grid grid-cols-2 gap-4">
                <flux:input
                    wire:model="exit_time"
                    type="time"
                    label="Exit time"
                    description="Between 13:00 and 14:00"
                    required
                />
                <flux:input
                    wire:model="return_time"
                    type="time"
                    label="Return time"
                    description="By 14:00"
                />
            </div>

            <flux:input wire:model="reason" label="Reason" placeholder="Brief reason for exit…"/>

            @foreach(['employee_id','date','exit_time','return_time'] as $field)
                @error($field)
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            @endforeach
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showAddModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="saveMovement" variant="primary" icon="check">Save movement</flux:button>
        </div>
    </flux:modal>
</div>
