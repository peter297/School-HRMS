
<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('leaves.index') }}" icon="arrow-left" variant="ghost" size="sm" />
        <div>
            <flux:heading size="xl">New leave application</flux:heading>
            <flux:subheading>Submit a leave request for a staff member</flux:subheading>
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

            <flux:select wire:model.live="leave_type_id" label="Leave type" required>
                <flux:select.option value="">Select leave type…</flux:select.option>
                @foreach($leaveTypes as $type)
                    <flux:select.option value="{{ $type->id }}">
                        {{ $type->name }} ({{ $type->days_allowed }} days/year · {{ $type->is_paid ? 'Paid' : 'Unpaid' }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input
                    wire:model.live="start_date"
                    type="date"
                    label="Start date"
                    required
                />
                <flux:input
                    wire:model.live="end_date"
                    type="date"
                    label="End date"
                    required
                />
            </div>

            @if($days_requested > 0)
                <flux:callout variant="info" icon="calendar-days">
                    <flux:callout.text>
                        This covers <strong>{{ $days_requested }} working day(s)</strong>
                        (weekends excluded).
                    </flux:callout.text>
                </flux:callout>
            @endif

            @error('days_requested')
                <flux:callout variant="danger" icon="x-circle">
                    <flux:callout.text>{{ $message }}</flux:callout.text>
                </flux:callout>
            @enderror

            <flux:textarea
                wire:model="reason"
                label="Reason"
                placeholder="Brief reason for leave (optional)…"
                rows="3"
            />

            <flux:separator />

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('leaves.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Submit application</flux:button>
            </div>

        </form>
    </flux:card>
</div>
