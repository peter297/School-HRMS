{{-- resources/views/livewire/employees/create.blade.php --}}

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('employees.index') }}" icon="arrow-left" variant="ghost" size="sm" />
        <div>
            <flux:heading size="xl">Add employee</flux:heading>
            <flux:subheading>Create a new staff record</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6">

            {{-- Personal information --}}
            <div>
                <flux:heading size="sm" class="mb-4">Personal information</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:input wire:model="staff_number" label="Staff number" placeholder="e.g. SCH-001" required />
                    <flux:select wire:model="gender" label="Gender">
                        <flux:select.option value="">Select gender</flux:select.option>
                        <flux:select.option value="male">Male</flux:select.option>
                        <flux:select.option value="female">Female</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="first_name" label="First name" placeholder="John" required />
                    <flux:input wire:model="last_name" label="Last name" placeholder="Doe" required />
                    <flux:input wire:model="email" type="email" label="Email address"
                        placeholder="john@school.ac.ke" />
                    <flux:input wire:model="phone" label="Phone number" placeholder="+254 7XX XXX XXX" />
                    <flux:input type="number" wire:model="national_id" label="National ID" placeholder="12345678" />
                </div>
            </div>

            <flux:separator />

            {{-- Employment details --}}
            <div>
                <flux:heading size="sm" class="mb-4">Employment details</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:select wire:model="line_manager_id" label="Line manager">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach ($employees as $emp)
                            @if ($emp->id !== ($employee->id ?? null))
                                <flux:select.option value="{{ $emp->id }}">
                                    {{ $emp->full_name }} ({{ $emp->staff_number }})
                                </flux:select.option>
                            @endif
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="staff_type" label="Staff type" required>
                        <flux:select.option value="">Select staff type</flux:select.option>
                        <flux:select.option value="teacher">Teacher</flux:select.option>
                        <flux:select.option value="admin">Admin Staff</flux:select.option>
                        <flux:select.option value="support_staff">Support Staff</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="branch" label="Branch" required>
                        <flux:select.option value="">Select branch…</flux:select.option>
                        <flux:select.option value="juja_road">Juja Road</flux:select.option>
                        <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                        <flux:select.option value="south_c">South C</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="division" label="Division" required>
                        <flux:select.option value="">Select division</flux:select.option>
                        <flux:select.option value="eye">Early Years Education</flux:select.option>
                        <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                        <flux:select.option value="junior_school">Junior School</flux:select.option>
                        <flux:select.option value="administration">Administration</flux:select.option>
                        <flux:select.option value="support">Support</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="job_title" label="Job title" placeholder="e.g. Class Teacher" />
                    <flux:input wire:model="date_of_joining" type="date" label="Date of joining" required />
                    <flux:select wire:model="employment_status" label="Status" required>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                        <flux:select.option value="on_leave">On Leave</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <flux:separator />

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('employees.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check">
                    Save employee
                </flux:button>
            </div>

        </form>
    </flux:card>
</div>
