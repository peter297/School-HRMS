{{-- resources/views/livewire/contracts/index.blade.php --}}

<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Contracts</flux:heading>
            <flux:subheading>Track and manage staff contracts</flux:subheading>
        </div>
        <flux:button href="{{ route('contracts.create') }}" icon="plus" variant="primary">
            New contract
        </flux:button>
    </div>

    {{-- Expiring soon alert --}}
    @if($expiringCount > 0)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
            <flux:callout.heading>{{ $expiringCount }} contract(s) expiring soon</flux:callout.heading>
            <flux:callout.text>
                These contracts are within their renewal alert window.
                <button wire:click="$set('showExpiring', true)" class="underline ml-1">View them</button>
            </flux:callout.text>
        </flux:callout>
    @endif

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- Filters --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search employee name or staff no…"
                icon="magnifying-glass"
            />
            <flux:select wire:model.live="filterType" placeholder="All contract types">
                <flux:select.option value="">All types</flux:select.option>
                <flux:select.option value="permanent">Permanent</flux:select.option>
                <flux:select.option value="fixed_term">Fixed Term</flux:select.option>
                <flux:select.option value="probation">Probation</flux:select.option>
                <flux:select.option value="part_time">Part Time</flux:select.option>
                <flux:select.option value="volunteer">Volunteer</flux:select.option>
            </flux:select>
            <flux:select wire:model.live="filterStatus" placeholder="All statuses">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="expired">Expired</flux:select.option>
                <flux:select.option value="terminated">Terminated</flux:select.option>
                <flux:select.option value="renewed">Renewed</flux:select.option>
            </flux:select>
            <div class="flex items-center gap-2">
                <flux:checkbox wire:model.live="showExpiring" id="expiring" />
                <label for="expiring" class="text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    Expiring soon only
                </label>
            </div>
        </div>
    </flux:card>

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Employee</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Type</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Start Date</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">End Date</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Duration</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Expiry</th>
                        <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($contracts as $contract)
                        <tr wire:key="{{ $contract->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $contract->employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400 mt-0.5">
                                    {{ $contract->employee->staff_number }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
                                {{ $contract->contract_type_label }}
                            </td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
                                {{ $contract->start_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
                                {{ $contract->end_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
                                {{ $contract->duration }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClasses = match($contract->status) {
                                        'active'     => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                        'expired'    => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                        'terminated' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400',
                                        'renewed'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                                        default      => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $badgeClasses }}">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($contract->days_until_expiry === null)
                                    <span class="text-zinc-400 text-sm">—</span>
                                @elseif($contract->days_until_expiry < 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400">
                                        Expired
                                    </span>
                                @elseif($contract->is_expiring_soon)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400">
                                        {{ $contract->days_until_expiry }}d left
                                    </span>
                                @else
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $contract->days_until_expiry }}d
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('contracts.edit', $contract) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:text-zinc-300 dark:hover:bg-zinc-700 transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                    </a>
                                    <button
                                        wire:click="deleteContract({{ $contract->id }})"
                                        wire:confirm="Remove this contract record?"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-zinc-500 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center text-zinc-400 dark:text-zinc-600">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <span class="text-sm">No contracts found.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
