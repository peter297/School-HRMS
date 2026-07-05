

<div>
    <div class="mb-6">
        <flux:heading size="xl">Import attendance</flux:heading>
        <flux:subheading>Upload a biometric Excel export to process attendance and generate incidents</flux:subheading>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- XLSX Import card --}}
        <flux:card>
            <flux:heading size="sm" class="mb-1">Upload biometric Excel</flux:heading>
            <flux:subheading class="mb-4">
                Required columns: <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">staff_number</code>,
                <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">date</code>,
                <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">Check-in</code>,
                <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 rounded">Check-out</code>
            </flux:subheading>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Excel file (.xlsx)
                    </label>
                    <input
                        type="file"
                        wire:model="importFile"
                        accept=".xlsx,.xls"
                        class="block w-full text-sm text-zinc-500
                               file:mr-4 file:py-2 file:px-4 file:rounded-lg
                               file:border-0 file:text-sm file:font-medium
                               file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200"
                    />
                    @error('importFile')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if(count($importErrors) > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 space-y-1">
                        <p class="text-sm font-medium text-red-600">{{ count($importErrors) }} row(s) skipped:</p>
                        @foreach($importErrors as $err)
                            <p class="text-xs text-red-500">• {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <flux:button
                    wire:click="importAttendance"
                    wire:loading.attr="disabled"
                    variant="primary"
                    icon="arrow-up-tray"
                    class="w-full"
                >
                    <span wire:loading.remove wire:target="importAttendance">Import & process</span>
                    <span wire:loading wire:target="importAttendance">Processing…</span>
                </flux:button>
            </div>
        </flux:card>

        {{-- Mark absent card --}}
        <flux:card>
            <flux:heading size="sm" class="mb-1">Mark missing as absent</flux:heading>
            <flux:subheading class="mb-4">
                Any active employee with no biometric record on this date will be marked absent and an incident generated.
            </flux:subheading>

            <div class="space-y-4">
                <flux:input
                    wire:model="absentDate"
                    type="date"
                    label="Select date"
                />
                @error('absentDate')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror

                <flux:button
                    wire:click="markMissingAbsent"
                    wire:confirm="Mark all employees with no record on this date as absent?"
                    variant="ghost"
                    icon="x-circle"
                    class="w-full"
                >
                    Mark absent
                </flux:button>
            </div>
        </flux:card>
    </div>

    {{-- Format reference --}}
    <flux:card class="mt-6">
        <flux:heading size="sm" class="mb-3">Expected Excel format</flux:heading>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">staff_number</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">date</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Check-in</th>
                        <th class="text-left py-2 px-3 text-zinc-500 font-medium">Check-out</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                        <td class="py-2 px-3 font-mono text-xs">SCH-001</td>
                        <td class="py-2 px-3 font-mono text-xs">2025-07-01</td>
                        <td class="py-2 px-3 font-mono text-xs">06:43</td>
                        <td class="py-2 px-3 font-mono text-xs">16:15</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-mono text-xs">SCH-002</td>
                        <td class="py-2 px-3 font-mono text-xs">2025-07-01</td>
                        <td class="py-2 px-3 font-mono text-xs">07:12</td>
                        <td class="py-2 px-3 font-mono text-xs">16:05</td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- <p class="text-xs text-zinc-400 mt-3">
            Dates and times may be Excel serial format or text (dd/mm/yyyy, HH:mm). Both are handled automatically.
        </p> --}}
    </flux:card>
</div>
