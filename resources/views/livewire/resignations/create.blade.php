<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('resignations.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Record resignation</flux:heading>
            <flux:subheading>Log a new staff resignation</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">

            <flux:select wire:model.live="employee_id" label="Employee" required>
                <flux:select.option value="">Select employee…</flux:select.option>
                @foreach($employees as $emp)
                    <flux:select.option value="{{ $emp->id }}">
                        {{ $emp->full_name }} ({{ $emp->staff_number }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input
                    wire:model.live="resignation_date"
                    type="date"
                    label="Resignation date"
                    required
                />
                <flux:select wire:model.live="notice_period" label="Notice period" required>
                    <flux:select.option value="1_week">1 Week</flux:select.option>
                    <flux:select.option value="2_weeks">2 Weeks</flux:select.option>
                    <flux:select.option value="1_month">1 Month</flux:select.option>
                    <flux:select.option value="2_months">2 Months</flux:select.option>
                    <flux:select.option value="3_months">3 Months</flux:select.option>
                </flux:select>
                <flux:input
                    wire:model="notice_end_date"
                    type="date"
                    label="Notice end date"
                    description="Auto-calculated from notice period"
                    required
                />
                <flux:input
                    wire:model="last_working_day"
                    type="date"
                    label="Last working day"
                    description="Leave blank if not yet confirmed"
                />
            </div>

            <flux:select wire:model="reason" label="Reason for leaving" required>
                <flux:select.option value="">Select reason…</flux:select.option>
                <flux:select.option value="personal">Personal reasons</flux:select.option>
                <flux:select.option value="better_opportunity">Better opportunity</flux:select.option>
                <flux:select.option value="relocation">Relocation</flux:select.option>
                <flux:select.option value="health">Health reasons</flux:select.option>
                <flux:select.option value="further_studies">Further studies</flux:select.option>
                <flux:select.option value="retirement">Retirement</flux:select.option>
                <flux:select.option value="other">Other</flux:select.option>
            </flux:select>

            <flux:textarea
                wire:model="reason_details"
                label="Reason details"
                placeholder="Additional details about the reason for leaving…"
                rows="3"
            />

            <flux:textarea
                wire:model="notes"
                label="HR notes"
                placeholder="Internal notes…"
                rows="2"
            />

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('resignations.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Record resignation</flux:button>
            </div>

        </form>
    </flux:card>
</div>
