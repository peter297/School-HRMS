<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('disciplinary.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Open disciplinary case</flux:heading>
            <flux:subheading>A case number will be auto-generated</flux:subheading>
        </div>
    </div>

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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:select wire:model="category" label="Category" required>
                    <flux:select.option value="">Select category…</flux:select.option>
                    <flux:select.option value="misconduct">Misconduct</flux:select.option>
                    <flux:select.option value="insubordination">Insubordination</flux:select.option>
                    <flux:select.option value="absenteeism">Absenteeism</flux:select.option>
                    <flux:select.option value="performance">Poor performance</flux:select.option>
                    <flux:select.option value="harassment">Harassment</flux:select.option>
                    <flux:select.option value="theft">Theft</flux:select.option>
                    <flux:select.option value="other">Other</flux:select.option>
                </flux:select>
                <flux:input
                    wire:model="incident_date"
                    type="date"
                    label="Incident date"
                    required
                />
            </div>

            <flux:textarea
                wire:model="description"
                label="Description of incident"
                placeholder="Describe the incident or conduct in detail. This will appear in the show cause letter…"
                rows="5"
                required
            />

            <flux:callout variant="info" icon="information-circle">
                <flux:callout.text>
                    Opening this case will set the stage to
                    <strong>Show cause issued</strong>. You can then generate
                    the show cause letter from the case page.
                </flux:callout.text>
            </flux:callout>

            <flux:separator/>

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('disciplinary.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary" icon="check">Open case</flux:button>
            </div>

        </form>
    </flux:card>
</div>