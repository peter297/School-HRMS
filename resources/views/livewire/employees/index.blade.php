{{-- resources/views/livewire/employees/index.blade.php --}}

<div>
    {{-- Header --}}
    <div class="flex items-center flex-wrap  justify-between mb-6">
        <div>
            <flux:heading size="xl">Employees</flux:heading>
            <flux:subheading>Manage all staff records</flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <flux:button wire:click='downloadTemplate' icon="arrow-down-tray" variant="ghost" size="sm">
                Template
            </flux:button>
            <flux:button wire:click='openImportModal' icon="arrow-up-tray" variant="ghost" size="sm" >
                Import
            </flux:button>
            <flux:button wire:click='exportSelected' icon="arrow-down-tray" variant="ghost" size="sm">
                {{ count($selectedIds) > 0 ? 'Export (' . count($selectedIds) . ')' :  'Export All'}}
            </flux:button>
            <flux:button href="{{ route('employees.create') }}" icon="plus" variant="primary">
            Add employee
        </flux:button>
        </div>

    </div>



     {{-- <flux:toast position="top end" /> --}}
    {{-- Flash message --}}
    @if(session('success'))


        {{-- <div class="mb-4">
            <flux:alert variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:alert>
        </div> --}}
    {{-- <flux:toast position="top end" /> --}}

    @endif

    {{-- Filters --}}
    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search name, staff no, email…"
                icon="magnifying-glass"/>
    </div>

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:select wire:model.live="filterType" placeholder="All staff types">
                <flux:select.option value="">All types</flux:select.option>
                <flux:select.option value="teacher">Teacher</flux:select.option>
                <flux:select.option value="admin">Admin Staff</flux:select.option>
                <flux:select.option value="support_staff">Support Staff</flux:select.option>
            </flux:select>
            <flux:select wire:model.live='filterBranch'>
                 <flux:select.option value="">All Branches</flux:select.option>
                 <flux:select.option value="juja_road">Juja Road</flux:select.option>
                 <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                 <flux:select.option value="south_c">South C</flux:select.option>
            </flux:select>
            {{-- <flux:select wire:model.live="filterDivision" placeholder="All divisions">
                <flux:select.option value="">All divisions</flux:select.option>
                <flux:select.option value="eye">Early Years Education</flux:select.option>
                <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                <flux:select.option value="junior_school">Junior School</flux:select.option>
                <flux:select.option value="administration">Administration</flux:select.option>
                <flux:select.option value="support">Support</flux:select.option>
            </flux:select> --}}
            <flux:select wire:model.live="filterStatus" placeholder="All statuses">
                <flux:select.option value="">All statuses</flux:select.option>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
                <flux:select.option value="on_leave">On Leave</flux:select.option>
            </flux:select>


                 <flux:select wire:model.live="perPage" placeholder="Per page">
                <flux:select.option value="10">10 entries</flux:select.option>
                <flux:select.option value="25">25 entries</flux:select.option>
                <flux:select.option value="50">50 entries</flux:select.option>
                <flux:select.option value="100">100 entries</flux:select.option>
            </flux:select>


        </div>
    </flux:card>

    @if (count($selectedIds) > 0)

        <div class="flex items-center gap-3 mb-3 px-1">
            <span class="text-sm text-zinc-500">
                {{ count($selectedIds) }} employess(s) selected
            </span>
            <flux:button wire:click="$set('selectedIds', [])" size="sm" variant="ghost">
                Clear Selection
            </flux:button>
            <flux:button wire:click='exportSelected' size="sm" icon="arrow-down-tray" variant="primary">
                Export Selected
            </flux:button>
        </div>

    @endif


    <flux:card>
     <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
    <table class="w-full text-sm text-left">
        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                <th class="text-left py-3 px-3 w-10">
                    <input type="checkbox" wire:model.live="selectAll"
                    class="rounded border-zinc-300"/>
                </th>
                <th wire:click="sort('staff_number')"
                    class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-zinc-100 select-none">
                    Staff No.
                </th>
                <th wire:click="sort('first_name')"
                    class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-zinc-100 select-none">
                    Name
                </th>
                <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Branch</th>
                <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Gender</th>
                <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Staff Type</th>
                <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Job Title</th>
                <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                <th class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
            @forelse($employees as $employee)
            <tr wire:key="{{ $employee->id }}"
                class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                <td class="py-3 px-3">
                    <input
                    type="checkbox"
                    wire:model.live="selectedIds"
                    value="{{ $employee->id }}"
                    class="rounded border-zinc-300"
                    />
                </td>
                <td class="px-4 py-3">
                    <span class="font-mono text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $employee->staff_number }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $employee->full_name }}</div>
                    <div class="text-xs text-zinc-400">{{ $employee->email }}</div>
                </td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ $employee->branch_label }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ $employee->gender }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ $employee->staff_type }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ $employee->job_title ?? '—' }}</td>
                <td class="px-4 py-3">
                   @php
                        $badgeClass = match($employee->employment_status) {
                            'active'   => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'on_leave' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            default    => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{$badgeClass}}">
                        {{ ucfirst(str_replace('_', ' ', $employee->employment_status)) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('employees.edit', $employee) }}"
                            class="p-1.5 rounded text-zinc-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </a>
                        <button
                            wire:click="deleteEmployee({{ $employee->id }})"
                            wire:confirm="Are you sure you want to remove {{ $employee->full_name }}?"
                            class="p-1.5 rounded text-zinc-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-zinc-400 dark:text-zinc-500">
                    No employees found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
     </flux:card>

     <flux:modal wire:model="showImportModal" class="max-w-lg">
        <flux:heading size="lg">Import Employees</flux:heading>
        <flux:subheading>
            Upload an Excel file to bulk-import staff records. Existing staff numbers will be skipped.
        </flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:callout variant="info" icon="information-circle">
                <flux:callout.text>
                    Not sure of the format?
                    <button wire:click="downloadTemplate" class="underline font-medium">
                        Download the import template
                    </button>
                    first.
                </flux:callout.text>
            </flux:callout>

            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 text-xs text-zinc-500 space-y-1">
                <p class="font-semibold text-zinc-600 dark:text-zinc-300 mb-2">Valid column values</p>
                <p><strong>staff_type:</strong> teacher_eye · teacher_upper_primary · teacher_junior · admin · support_staff</p>
                <p><strong>division:</strong> eye · upper_primary · junior_school · administration · support</p>
                <p><strong>branch:</strong> juja_road · kitisuru · south_c</p>
                <p><strong>gender:</strong> male · female </p>
                <p><strong>status:</strong> active · inactive · on_leave</p>
                <p><strong>date_of_joining:</strong> dd/mm/yyyy</p>
            </div>

            <div>
                <label for="" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Excel file (.xlsx)
                </label>
                <input
                    type="file"
                    wire:model="importFile"
                    accept=".xlsx, .xls"
                    class="block w-full text-sm text-zinc-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-lg file:border-0
                           file:text-sm file:font-medium
                           file:bg-zinc-100 file:text-zinc-700
                           hover:file:bg-zinc-200"
                />
                @error('importFile')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror

            </div>
            @if(count($importErrors) > 0)
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 space-y-1">
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                        {{ count($importErrors) }} row(s) had errors:
                    </p>
                    @foreach($importErrors as $error)
                        <p class="text-xs text-red-500">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif


        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showImportModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button
                wire:click="importEmployees"
                wire:loading.attr="disabled"
                variant="primary"
                icon="arrow-up-tray"
            >
                <span wire:loading.remove wire:target="importEmployees">Import</span>
                <span wire:loading wire:target="importEmployees">Importing…</span>
            </flux:button>
        </div>

     </flux:modal>
</div>
