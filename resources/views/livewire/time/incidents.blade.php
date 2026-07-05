
<div>
    <div class="mb-6">
        <flux:heading size="xl">Lateness Incidents</flux:heading>
        <flux:subheading>Auto-generated time violations for review and resolution</flux:subheading>
    </div>

    @if($unresolvedCount > 0)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
            <flux:callout.heading>{{ $unresolvedCount }} unresolved incident(s)</flux:callout.heading>
            <flux:callout.text>
                These require HR review.
                <button wire:click="$set('filterResolved', '0')" class="underline ml-1">Show unresolved</button>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search employee…" icon="magnifying-glass"/>
            <flux:select wire:model.live="filterType">
                <flux:select.option value="">All types</flux:select.option>
                <flux:select.option value="late_arrival">Late arrival</flux:select.option>
                <flux:select.option value="early_departure">Early departure</flux:select.option>
                <flux:select.option value="absent">Absent</flux:select.option>
                <flux:select.option value="late_and_early">Late & early</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterResolved">
                <flux:select.option value="">All</flux:select.option>
                <flux:select.option value="0">Unresolved</flux:select.option>
                <flux:select.option value="1">Resolved</flux:select.option>
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
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Date</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Type</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Details</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Status</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Resolved by</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($incidents as $incident)
                        <tr wire:key="{{ $incident->id }}" class="{{ !$incident->resolved ? 'bg-yellow-50/30 dark:bg-yellow-900/10' : '' }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $incident->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">{{ $incident->employee->staff_number }}</div>
                            </td>
                            <td class="py-3 px-3 text-zinc-600 dark:text-zinc-300">
                                {{ $incident->date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="{{ $incident->type_color }}" size="sm">
                                    {{ $incident->type_label }}
                                </flux:badge>
                            </td>
                            <td class="py-3 px-3 text-zinc-500 max-w-xs">
                                <span class="text-xs">{{ $incident->details }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if($incident->resolved)
                                    <flux:badge color="green" size="sm">Resolved</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Open</flux:badge>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-xs text-zinc-500">
                                @if($incident->resolved)
                                    {{ $incident->resolvedBy?->name }}<br>
                                    <span class="text-zinc-400">{{ $incident->resolved_at?->format('d M Y') }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if(!$incident->resolved)
                                    <flux:button
                                        wire:click="openResolveModal({{ $incident->id }})"
                                        size="sm" variant="ghost" icon="check"
                                    >
                                        Resolve
                                    </flux:button>
                                @else
                                    <span class="text-xs text-zinc-400 italic max-w-[150px] block">
                                        {{ $incident->resolution_note }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-zinc-400">No incidents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $incidents->links() }}</div>
    </flux:card>

    {{-- Resolve modal --}}
    <flux:modal wire:model="showResolveModal" class="max-w-md">
        <flux:heading size="lg">Resolve incident</flux:heading>
        <flux:subheading>Provide a resolution note before closing this incident.</flux:subheading>
        <div class="mt-4">
            <flux:textarea
                wire:model="resolutionNote"
                label="Resolution note"
                placeholder="Describe how this was addressed…"
                rows="3"
            />
            @error('resolutionNote')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showResolveModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="confirmResolve" variant="primary" icon="check">Mark resolved</flux:button>
        </div>
    </flux:modal>
</div>
