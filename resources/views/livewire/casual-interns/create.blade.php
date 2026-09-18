{{-- resources/views/livewire/casual-interns/create.blade.php --}}

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('casual-interns.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Add casual / intern</flux:heading>
            <flux:subheading>Create a new casual worker or intern record</flux:subheading>
        </div>
    </div>

    <flux:card class="max-w-3xl">
        <form wire:submit="save" class="space-y-6">

            {{-- Type selector --}}
            <div class="grid grid-cols-2 gap-4">
                <button
                    type="button"
                    wire:click="$set('type', 'casual')"
                    class="p-4 rounded-lg border-2 text-left transition-all
                        {{ $type === 'casual'
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                            : 'border-zinc-200 dark:border-zinc-700' }}"
                >
                    <p class="font-medium text-zinc-900 dark:text-zinc-100">Casual worker</p>
                    <p class="text-xs text-zinc-400 mt-1">Short-term, daily-rated engagement</p>
                </button>
                <button
                    type="button"
                    wire:click="$set('type', 'intern')"
                    class="p-4 rounded-lg border-2 text-left transition-all
                        {{ $type === 'intern'
                            ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                            : 'border-zinc-200 dark:border-zinc-700' }}"
                >
                    <p class="font-medium text-zinc-900 dark:text-zinc-100">Intern</p>
                    <p class="text-xs text-zinc-400 mt-1">Institutional attachment or placement</p>
                </button>
            </div>

            <flux:separator/>

            {{-- Personal details --}}
            <div>
                <flux:heading size="sm" class="mb-4">Personal details</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:input wire:model="staff_number" label="Staff / ID number" placeholder="CAS-001" required/>
                    <flux:input wire:model="national_id"  label="National ID" placeholder="12345678"/>
                    <flux:input wire:model="first_name"   label="First name" required/>
                    <flux:input wire:model="last_name"    label="Last name"  required/>
                    <flux:input wire:model="email"        type="email" label="Email"/>
                    <flux:input wire:model="phone"        label="Phone"/>
                </div>
            </div>

            <flux:separator/>

            {{-- Placement details --}}
            <div>
                <flux:heading size="sm" class="mb-4">Placement details</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:select wire:model="division" label="Division" required>
                        <flux:select.option value="">Select division…</flux:select.option>
                        <flux:select.option value="eye">Early Years Education</flux:select.option>
                        <flux:select.option value="upper_primary">Upper Primary</flux:select.option>
                        <flux:select.option value="junior_school">Junior School</flux:select.option>
                        <flux:select.option value="administration">Administration</flux:select.option>
                        <flux:select.option value="support">Support</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="branch" label="Branch" required>
                        <flux:select.option value="">Select branch…</flux:select.option>
                        <flux:select.option value="juja_road">Juja Road</flux:select.option>
                        <flux:select.option value="kitisuru">Kitisuru</flux:select.option>
                        <flux:select.option value="south_c">South C</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="job_title"  label="Job title / Role"/>
                    <flux:input wire:model="supervisor" label="Supervisor name"/>
                    <flux:input wire:model="start_date" type="date" label="Start date" required/>
                    <flux:input wire:model="end_date"   type="date" label="End date"
                        description="Leave blank if open-ended"/>
                    <flux:input
                        wire:model="daily_rate"
                        type="number"
                        label="Daily rate (KES)"
                        placeholder="0.00"
                        step="0.01"
                    />
                </div>
            </div>

            {{-- Intern-only fields --}}
            @if($type === 'intern')
                <flux:separator/>
                <div>
                    <flux:heading size="sm" class="mb-4">Institution details</flux:heading>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:input
                            wire:model="institution"
                            label="Institution / University"
                            placeholder="e.g. Kenyatta University"
                        />
                        <flux:input
                            wire:model="course"
                            label="Course / Programme"
                            placeholder="e.g. Bachelor of Education"
                        />
                    </div>
                </div>
            @endif

            <flux:textarea wire:model="notes" label="Notes" rows="2"/>

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('casual-interns.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Save record</flux:button>
            </div>

        </form>
    </flux:card>
</div>