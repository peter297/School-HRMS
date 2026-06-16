{{-- resources/views/livewire/contracts/create.blade.php --}}

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('contracts.index') }}" icon="arrow-left" variant="ghost" size="sm" />
        <div>
            <flux:heading size="xl">New contract</flux:heading>
            <flux:subheading>Assign a contract to a staff member</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-6xl">
        <form wire:submit="save" class="space-y-6">

            <flux:select wire:model="employee_id" label="Employee" required>
                <flux:select.option value="">Select employee…</flux:select.option>
                @foreach($employees as $emp)
                    <flux:select.option value="{{ $emp->id }}">
                        {{ $emp->full_name }} ({{ $emp->staff_number }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:select wire:model="contract_type" label="Contract type" required>
                    <flux:select.option value="">Select type…</flux:select.option>
                    <flux:select.option value="permanent">Permanent</flux:select.option>
                    <flux:select.option value="fixed-term">Fixed Term</flux:select.option>
                    <flux:select.option value="probation">Probation</flux:select.option>
                    <flux:select.option value="part-time">Part Time</flux:select.option>
                    <flux:select.option value="internship">Internship</flux:select.option>
                </flux:select>

                <flux:select wire:model="status" label="Status" required>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="expired">Expired</flux:select.option>
                    <flux:select.option value="terminated">Terminated</flux:select.option>
                    <flux:select.option value="renewed">Renewed</flux:select.option>
                </flux:select>

                <flux:input wire:model="start_date" type="date" label="Start date" required />

                <flux:input wire:model="end_date" type="date" label="End date"
                    description="Leave empty for permanent/open-ended contracts" />

                <flux:input
                    wire:model="renewal_alert_days"
                    type="number"
                    label="Renewal alert (days)"
                    description="Alert HR this many days before expiry"
                    min="1" max="365"
                />
            </div>

            <flux:textarea
                wire:model="notes"
                label="Notes"
                placeholder="Any additional details about this contract…"
                rows="3"
            />

            <flux:separator />

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('contracts.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Save contract</flux:button>
            </div>

        </form>
    </flux:card>
</div>
