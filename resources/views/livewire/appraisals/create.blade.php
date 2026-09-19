<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('appraisals.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">New appraisal</flux:heading>
            <flux:subheading>Initiate a performance appraisal for a staff member</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-xl">
        <form wire:submit="save" class="space-y-6">

            <flux:select wire:model="employee_id" label="Employee" required>
                <flux:select.option value="">Select employee…</flux:select.option>
                @foreach($employees as $emp)
                    <flux:select.option value="{{ $emp->id }}">
                        {{ $emp->full_name }} ({{ $emp->staff_number }}) — {{ $emp->staff_type_label }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:select wire:model.live="type" label="Appraisal type" required>
                    <flux:select.option value="annual">Annual appraisal</flux:select.option>
                    <flux:select.option value="mid_year">Mid-year review</flux:select.option>
                    <flux:select.option value="probation">Probation review</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="year" label="Year" required>
                    @foreach(range(now()->year + 1, now()->year - 3) as $y)
                        <flux:select.option value="{{ $y }}">{{ $y }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:input
                wire:model="period"
                label="Period label"
                description="e.g. 2026 — Annual or Term 1 2026"
                required
            />

            <flux:callout variant="info" icon="information-circle">
                <flux:callout.text>
                    KPIs will be auto-populated based on the employee's staff type.
                    The line manager fills in the ratings via their staff portal.
                </flux:callout.text>
            </flux:callout>

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('appraisals.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Create appraisal</flux:button>
            </div>

        </form>
    </flux:card>
</div>