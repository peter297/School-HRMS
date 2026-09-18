

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('casual-interns.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">{{ $casualIntern->full_name }}</flux:heading>
            <flux:subheading>
                {{ $casualIntern->staff_number }} ·
                <flux:badge color="{{ $casualIntern->type_color }}" size="sm">
                    {{ $casualIntern->type_label }}
                </flux:badge>
            </flux:subheading>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if($casualIntern->promoted)
        <flux:callout variant="success" icon="arrow-up-circle" class="mb-4">
            <flux:callout.heading>Promoted to full employee</flux:callout.heading>
            <flux:callout.text>
                Promoted on {{ $casualIntern->promoted_date?->format('d M Y') }}
                @if($casualIntern->promotedToEmployee)
                    · New staff number:
                    <strong>{{ $casualIntern->promotedToEmployee->staff_number }}</strong>
                @endif
            </flux:callout.text>
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details --}}
        <div class="lg:col-span-2 space-y-4">
            <flux:card>
                <flux:heading size="sm" class="mb-4">Personal details</flux:heading>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-400">Full name</dt>
                        <dd class="font-medium mt-1">{{ $casualIntern->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Staff / ID number</dt>
                        <dd class="font-mono mt-1">{{ $casualIntern->staff_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">National ID</dt>
                        <dd class="mt-1">{{ $casualIntern->national_id ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Email</dt>
                        <dd class="mt-1">{{ $casualIntern->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Phone</dt>
                        <dd class="mt-1">{{ $casualIntern->phone ?? '—' }}</dd>
                    </div>
                </dl>
            </flux:card>

            <flux:card>
                <flux:heading size="sm" class="mb-4">Placement details</flux:heading>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-400">Type</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $casualIntern->type_color }}" size="sm">
                                {{ $casualIntern->type_label }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Status</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $casualIntern->status_color }}" size="sm">
                                {{ ucfirst($casualIntern->status) }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Division</dt>
                        <dd class="mt-1">{{ $casualIntern->division_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Branch</dt>
                        <dd class="mt-1">{{ $casualIntern->branch_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Job title</dt>
                        <dd class="mt-1">{{ $casualIntern->job_title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Supervisor</dt>
                        <dd class="mt-1">{{ $casualIntern->supervisor ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Start date</dt>
                        <dd class="mt-1">{{ $casualIntern->start_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">End date</dt>
                        <dd class="mt-1">{{ $casualIntern->end_date?->format('d M Y') ?? 'Open-ended' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Duration</dt>
                        <dd class="mt-1">{{ $casualIntern->duration }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Working days</dt>
                        <dd class="mt-1">{{ $casualIntern->days_worked }} days</dd>
                    </div>
                    @if($casualIntern->daily_rate)
                        <div>
                            <dt class="text-zinc-400">Daily rate</dt>
                            <dd class="mt-1">KES {{ number_format($casualIntern->daily_rate, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-400">Estimated total</dt>
                            <dd class="mt-1 font-medium text-green-600">{{ $casualIntern->total_earnings }}</dd>
                        </div>
                    @endif
                </dl>
            </flux:card>

            @if($casualIntern->type === 'intern' && ($casualIntern->institution || $casualIntern->course))
                <flux:card>
                    <flux:heading size="sm" class="mb-4">Institution details</flux:heading>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-zinc-400">Institution</dt>
                            <dd class="mt-1">{{ $casualIntern->institution ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-400">Course</dt>
                            <dd class="mt-1">{{ $casualIntern->course ?? '—' }}</dd>
                        </div>
                    </dl>
                </flux:card>
            @endif
        </div>

        {{-- Actions panel --}}
        <div class="space-y-4">

            {{-- Promote --}}
            @if(!$casualIntern->promoted && $casualIntern->status === 'active')
                <flux:card>
                    <flux:heading size="sm" class="mb-2">Promote to employee</flux:heading>
                    <p class="text-xs text-zinc-400 mb-4">
                        Convert this {{ $casualIntern->type_label }} to a full employee record.
                        All personal details will be carried over.
                    </p>
                    <flux:button
                        wire:click="openPromoteModal"
                        variant="primary"
                        icon="arrow-up-circle"
                        class="w-full"
                    >
                        Promote to employee
                    </flux:button>
                </flux:card>
            @endif

            {{-- Status update --}}
            @if(!$casualIntern->promoted)
                <flux:card>
                    <flux:heading size="sm" class="mb-3">Update status</flux:heading>
                    <div class="space-y-2">
                        @if($casualIntern->status !== 'active')
                            <flux:button
                                wire:click="updateStatus('active')"
                                variant="ghost" size="sm" class="w-full"
                            >
                                Mark active
                            </flux:button>
                        @endif
                        @if($casualIntern->status !== 'completed')
                            <flux:button
                                wire:click="updateStatus('completed')"
                                variant="ghost" size="sm" class="w-full"
                            >
                                Mark completed
                            </flux:button>
                        @endif
                        @if($casualIntern->status !== 'terminated')
                            <flux:button
                                wire:click="updateStatus('terminated')"
                                wire:confirm="Mark this record as terminated?"
                                variant="ghost" size="sm" class="w-full"
                            >
                                Mark terminated
                            </flux:button>
                        @endif
                    </div>
                </flux:card>
            @endif

            {{-- Edit --}}
            <flux:card>
                <flux:button
                    href="{{ route('casual-interns.edit', $casualIntern) }}"
                    variant="ghost"
                    icon="pencil"
                    class="w-full"
                >
                    Edit record
                </flux:button>
            </flux:card>

            <flux:card>
                <p class="text-xs text-zinc-400">Recorded by {{ $casualIntern->recordedBy->name }}</p>
                <p class="text-xs text-zinc-400 mt-1">{{ $casualIntern->created_at->format('d M Y H:i') }}</p>
            </flux:card>
        </div>
    </div>

    {{-- Promote modal --}}
    <flux:modal wire:model="showPromoteModal" class="max-w-lg">
        <flux:heading size="lg">Promote to full employee</flux:heading>
        <flux:subheading>
            A new employee record will be created for {{ $casualIntern->full_name }}.
            All personal details will be carried over automatically.
        </flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:input
                wire:model="promoteStaffNumber"
                label="New staff number"
                placeholder="SCH-XXX"
                required
            />
            @error('promoteStaffNumber')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <flux:select wire:model="promoteStaffType" label="Staff type" required>
                <flux:select.option value="teacher_eye">Teacher — EYE</flux:select.option>
                <flux:select.option value="teacher_upper_primary">Teacher — Upper Primary</flux:select.option>
                <flux:select.option value="teacher_junior">Teacher — Junior School</flux:select.option>
                <flux:select.option value="admin">Admin Staff</flux:select.option>
                <flux:select.option value="support_staff">Support Staff</flux:select.option>
            </flux:select>

            <flux:input
                wire:model="promoteJobTitle"
                label="Job title"
                placeholder="e.g. Class Teacher"
            />

            <flux:input
                wire:model="promoteDateOfJoining"
                type="date"
                label="Date of joining (as employee)"
                required
            />

            <flux:callout variant="info" icon="information-circle">
                <flux:callout.text>
                    The current {{ $casualIntern->type_label }} record will be marked as
                    <strong>completed</strong> after promotion.
                </flux:callout.text>
            </flux:callout>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showPromoteModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button wire:click="confirmPromote" variant="primary" icon="arrow-up-circle">
                Confirm promotion
            </flux:button>
        </div>
    </flux:modal>
</div>
