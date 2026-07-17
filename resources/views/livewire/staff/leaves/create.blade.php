<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('staff.leaves.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Apply for leave</flux:heading>
            <flux:subheading>Submit a new leave request</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-xl">
        <form wire:submit="save" class="space-y-5">

            <flux:select wire:model.live="leave_type_id" label="Leave type" required>
                <flux:select.option value="">Select leave type…</flux:select.option>
                @foreach($leaveTypes as $type)
                    <flux:select.option value="{{ $type->id }}">
                        {{ $type->name }} ({{ $type->days_allowed }} days/year · {{ $type->is_paid ? 'Paid' : 'Unpaid' }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model.live="start_date" type="date" label="Start date" required/>
                <flux:input wire:model.live="end_date"   type="date" label="End date"   required/>
            </div>

            @if($days_requested > 0)
                <flux:callout variant="info" icon="calendar-days">
                    <flux:callout.text>
                        This covers <strong>{{ $days_requested }} working day(s)</strong>.
                    </flux:callout.text>
                </flux:callout>
            @endif

            @error('days_requested')
                <flux:callout variant="danger" icon="x-circle">
                    <flux:callout.text>{{ $message }}</flux:callout.text>
                </flux:callout>
            @enderror

            <flux:textarea wire:model="reason" label="Reason" placeholder="Optional reason for leave…" rows="3"/>

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('staff.leaves.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="paper-airplane">Submit application</flux:button>
            </div>

        </form>
    </flux:card>
</div>
