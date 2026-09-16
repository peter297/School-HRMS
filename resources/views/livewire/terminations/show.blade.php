

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('terminations.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Termination — {{ $termination->employee->full_name }}</flux:heading>
            <flux:subheading>
                {{ $termination->employee->staff_number }} ·
                {{ $termination->termination_date->format('d M Y') }} ·
                {{ $termination->reason_label }}
            </flux:subheading>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details --}}
        <div class="lg:col-span-2 space-y-4">
            <flux:card>
                <flux:heading size="sm" class="mb-4">Termination details</flux:heading>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-400">Employee</dt>
                        <dd class="font-medium mt-1">{{ $termination->employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Staff number</dt>
                        <dd class="font-mono mt-1">{{ $termination->employee->staff_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Division</dt>
                        <dd class="mt-1">{{ $termination->employee->division_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Branch</dt>
                        <dd class="mt-1">{{ $termination->employee->branch_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Termination date</dt>
                        <dd class="mt-1">{{ $termination->termination_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Notice date</dt>
                        <dd class="mt-1">{{ $termination->notice_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Last working day</dt>
                        <dd class="mt-1">{{ $termination->last_working_day?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Status</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $termination->status_color }}" size="sm">
                                {{ $termination->status_label }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Reason</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $termination->reason_color }}" size="sm">
                                {{ $termination->reason_label }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-zinc-400">Details</dt>
                        <dd class="mt-1 text-zinc-600 dark:text-zinc-300">
                            {{ $termination->reason_details ?? '—' }}
                        </dd>
                    </div>
                    @if($termination->hr_notes)
                        <div class="col-span-2">
                            <dt class="text-zinc-400">HR notes (confidential)</dt>
                            <dd class="mt-1 text-zinc-500 italic">{{ $termination->hr_notes }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-zinc-400">Recorded by</dt>
                        <dd class="mt-1">{{ $termination->recordedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Recorded on</dt>
                        <dd class="mt-1">{{ $termination->created_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </flux:card>
        </div>

        {{-- Stage tracker --}}
        <div>
            <flux:card>
                <flux:heading size="sm" class="mb-4">Stage tracker</flux:heading>

                {{-- Progress --}}
                <div class="mb-5">
                    <div class="flex justify-between text-xs text-zinc-400 mb-1">
                        <span>Progress</span>
                        <span>{{ $termination->completion_percentage }}%</span>
                    </div>
                    <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-2">
                        <div
                            class="bg-red-500 h-2 rounded-full transition-all duration-500"
                            style="width: {{ $termination->completion_percentage }}%"
                        ></div>
                    </div>
                </div>

                {{-- Stage 1 — Initiated (always done) --}}
                <div class="flex items-center gap-3 py-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                        <flux:icon.check class="w-3 h-3 text-green-600"/>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Initiated</p>
                        <p class="text-xs text-zinc-400">{{ $termination->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Stage 2 — Notice served --}}
                <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        @if($termination->notice_served)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700 shrink-0"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Notice served</p>
                            @if($termination->notice_served_date)
                                <p class="text-xs text-zinc-400">{{ $termination->notice_served_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$termination->notice_served)
                        <flux:button wire:click="markStage('notice_served')" size="sm" variant="ghost">
                            Mark
                        </flux:button>
                    @endif
                </div>

                {{-- Stage 3 — Clearance done --}}
                <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        @if($termination->clearance_done)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700 shrink-0"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Clearance done</p>
                            @if($termination->clearance_done_date)
                                <p class="text-xs text-zinc-400">{{ $termination->clearance_done_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$termination->clearance_done)
                        <flux:button wire:click="markStage('clearance_done')" size="sm" variant="ghost">
                            Mark
                        </flux:button>
                    @endif
                </div>

                {{-- Stage 4 — Final pay --}}
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        @if($termination->final_pay_processed)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700 shrink-0"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Final pay processed</p>
                            @if($termination->final_pay_processed_date)
                                <p class="text-xs text-zinc-400">{{ $termination->final_pay_processed_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$termination->final_pay_processed)
                        <flux:button wire:click="markStage('final_pay_processed')" size="sm" variant="ghost">
                            Mark
                        </flux:button>
                    @endif
                </div>

                @if($termination->status === 'completed')
                    <div class="mt-4 bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                        <flux:icon.check-circle class="w-6 h-6 text-green-500 mx-auto mb-1"/>
                        <p class="text-sm font-medium text-green-600">Termination completed</p>
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</div>