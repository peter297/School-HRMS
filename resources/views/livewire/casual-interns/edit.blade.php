

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('casual-interns.show', $casualIntern) }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Edit record</flux:heading>
            <flux:subheading>{{ $casualIntern->full_name }} · {{ $casualIntern->staff_number }}</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input wire:model="first_name"  label="First name" required/>
                <flux:input wire:model="last_name"   label="Last name"  required/>
                <flux:input wire:model="email"       type="email" label="Email"/>
                <flux:input wire:model="phone"       label="Phone"/>
                <flux:input wire:model="national_id" label="National ID"/>
                <flux:select wire:model="type" label="Type" required>
                    <flux:select.option value="casual">Casual</flux:select.option>
                    <flux:select.option value="intern">Intern</flux:select.option>
                </flux:select>
                <flux:select wire:model="division" label="Division" required>
                    <flux:select.option value="eye">Early Years Education</flux:select.option>
                    <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                    <flux:select.option value="junior_school">Junior School</flux:select.option>
                    <flux:select.option value="administration">Administration</flux:select.option>
                    <flux:select.option value="support">Support</flux:select.option>
                </flux:select>
                <flux:select wire:model="branch" label="Branch" required>
                    <flux:select.option value="juja_road">Juja Road</flux:select.option>
                    <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                    <flux:select.option value="south_c">South C</flux:select.option>
                </flux:select>
                <flux:input wire:model="job_title"  label="Job title"/>
                <flux:input wire:model="supervisor" label="Supervisor"/>
                <flux:input wire:model="start_date" type="date" label="Start date" required/>
                <flux:input wire:model="end_date"   type="date" label="End date"/>
                <flux:input wire:model="daily_rate" type="number" label="Daily rate (KES)" step="0.01"/>
                <flux:select wire:model="status" label="Status" required>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                    <flux:select.option value="terminated">Terminated</flux:select.option>
                </flux:select>
            </div>

            @if($type === 'intern')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:input wire:model="institution" label="Institution"/>
                    <flux:input wire:model="course"      label="Course"/>
                </div>
            @endif

            <flux:textarea wire:model="notes" label="Notes" rows="2"/>

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('casual-interns.show', $casualIntern) }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check">Update record</flux:button>
            </div>

        </form>
    </flux:card>
</div>
