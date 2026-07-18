{{-- resources/views/livewire/employees/edit.blade.php --}}

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('employees.index') }}" icon="arrow-left" variant="ghost" size="sm" />
        <div>
            <flux:heading size="xl">Edit employee</flux:heading>
            <flux:subheading>{{ $employee->full_name }} · {{ $employee->staff_number }}</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6">

            <div>
                <flux:heading size="sm" class="mb-4">Personal information</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:input wire:model="staff_number" label="Staff number" required />
                    <flux:select wire:model="gender" label="Gender">
                        <flux:select.option value="">Select gender</flux:select.option>
                        <flux:select.option value="male">Male</flux:select.option>
                        <flux:select.option value="female">Female</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="first_name" label="First name" required />
                    <flux:input wire:model="last_name" label="Last name" required />
                    <flux:input wire:model="email" type="email" label="Email address" />
                    <flux:input wire:model="phone" label="Phone number" />
                    <flux:input wire:model="national_id" label="National ID" />
                </div>
            </div>

            <flux:separator />

            <div>
                <flux:heading size="sm" class="mb-4">Employment details</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:select wire:model="line_manager_id" label="Line manager">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach ($employees as $emp)
                            @if ($emp->id !== ($employees->id ?? null))
                                <flux:select.option value="{{ $emp->id }}">
                                    {{ $emp->full_name }} ({{ $emp->staff_number }})
                                </flux:select.option>
                            @endif
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="staff_type" label="Staff type" required>
                        <flux:select.option value="teacher">Teacher </flux:select.option>
                        <flux:select.option value="admin">Admin</flux:select.option>
                        <flux:select.option value="support_staff">Support Staff</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="branch" label="Branch" required>
                        <flux:select.option value="">Select branch…</flux:select.option>
                        <flux:select.option value="juja_road">Juja Road</flux:select.option>
                        <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                        <flux:select.option value="south_c">South C</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="division" label="Division" required>
                        <flux:select.option value="eye">EYE</flux:select.option>
                        <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                        <flux:select.option value="junior_school">Junior School</flux:select.option>
                        <flux:select.option value="administration">Administration</flux:select.option>
                        <flux:select.option value="support_services">Support</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="job_title" label="Job title" />
                    <flux:input wire:model="date_of_joining" type="date" label="Date of joining" required />
                    <flux:select wire:model="employment_status" label="Employment Status" required>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                        <flux:select.option value="on_leave">On Leave</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <flux:separator />

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('employees.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Update employee</flux:button>
            </div>

        </form>
    </flux:card>
</div>
