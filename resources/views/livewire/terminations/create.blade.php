{{-- resources/views/livewire/terminations/create.blade.php --}}

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('terminations.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Record termination</flux:heading>
            <flux:subheading>Log a staff termination</flux:subheading>
        </div>
    </div>

    <flux:callout variant="danger" icon="exclamation-triangle" class="mb-6 max-w-2xl">
        <flux:callout.heading>Important</flux:callout.heading>
        <flux:callout.text>
            Recording a termination will immediately set the employee's status to inactive.
            Ensure all HR procedures have been followed before proceeding.
        </flux:callout.text>
    </flux:callout>

    <flux:card class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">

            <flux:select wire:model="employee_id" label="Employee" required>
                <flux:select.option value="">Select employee…</flux:select.option>
                @foreach($employees as $emp)
                    <flux:select.option value="{{ $emp->id }}">
                        {{ $emp->full_name }} ({{ $emp->staff_number }}) — {{ $emp->division_label }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="reason" label="Reason for termination" required>
                <flux:select.option value="">Select reason…</flux:select.option>
                <flux:select.option value="misconduct">Misconduct</flux:select.option>
                <flux:select.option value="redundancy">Redundancy</flux:select.option>
                <flux:select.option value="contract_end">Contract end</flux:select.option>
                <flux:select.option value="performance">Poor performance</flux:select.option>
                <flux:select.option value="absenteeism">Absenteeism</flux:select.option>
                <flux:select.option value="other">Other</flux:select.option>
            </flux:select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input
                    wire:model="termination_date"
                    type="date"
                    label="Termination date"
                    required
                />
                <flux:input
                    wire:model="notice_date"
                    type="date"
                    label="Notice date"
                    description="Date notice was issued"
                />
                <flux:input
                    wire:model="last_working_day"
                    type="date"
                    label="Last working day"
                    description="Leave blank if not yet confirmed"
                />
            </div>

            <flux:textarea
                wire:model="reason_details"
                label="Details"
                placeholder="Describe the circumstances leading to this termination…"
                rows="4"
                required
            />

            <flux:textarea
                wire:model="hr_notes"
                label="Internal HR notes"
                placeholder="Confidential notes for HR record only…"
                rows="2"
            />

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('terminations.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="danger" icon="x-circle">Record termination</flux:button>
            </div>

        </form>
    </flux:card>
</div>