{{-- resources/views/livewire/resignations/show.blade.php --}}

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('resignations.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Resignation — {{ $resignation->employee->full_name }}</flux:heading>
            <flux:subheading>{{ $resignation->employee->staff_number }} · {{ $resignation->resignation_date->format('d M Y') }}</flux:subheading>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details --}}
        <div class="lg:col-span-2 space-y-4">
            <flux:card>
                <flux:heading size="sm" class="mb-4">Resignation details</flux:heading>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-400">Employee</dt>
                        <dd class="font-medium mt-1">{{ $resignation->employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Staff number</dt>
                        <dd class="font-mono mt-1">{{ $resignation->employee->staff_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Division</dt>
                        <dd class="mt-1">{{ $resignation->employee->division_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Branch</dt>
                        <dd class="mt-1">{{ $resignation->employee->branch_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Resignation date</dt>
                        <dd class="mt-1">{{ $resignation->resignation_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Notice period</dt>
                        <dd class="mt-1">{{ $resignation->notice_period_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Notice end date</dt>
                        <dd class="mt-1">{{ $resignation->notice_end_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Last working day</dt>
                        <dd class="mt-1">{{ $resignation->last_working_day?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Reason</dt>
                        <dd class="mt-1">{{ $resignation->reason_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Status</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $resignation->status_color }}" size="sm">
                                {{ $resignation->status_label }}
                            </flux:badge>
                        </dd>
                    </div>
                    @if($resignation->reason_details)
                        <div class="col-span-2">
                            <dt class="text-zinc-400">Reason details</dt>
                            <dd class="mt-1">{{ $resignation->reason_details }}</dd>
                        </div>
                    @endif
                    @if($resignation->notes)
                        <div class="col-span-2">
                            <dt class="text-zinc-400">HR notes</dt>
                            <dd class="mt-1 text-zinc-500 italic">{{ $resignation->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </flux:card>
        </div>

        {{-- Stage tracker --}}
        <div class="space-y-3">
            <flux:card>
                <flux:heading size="sm" class="mb-4">Stage tracker</flux:heading>

                {{-- Progress bar --}}
                <div class="mb-5">
                    <div class="flex justify-between text-xs text-zinc-400 mb-1">
                        <span>Progress</span>
                        <span>{{ $resignation->completion_percentage }}%</span>
                    </div>
                    <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-2">
                        <div
                            class="bg-green-500 h-2 rounded-full transition-all duration-500"
                            style="width: {{ $resignation->completion_percentage }}%"
                        ></div>
                    </div>
                </div>

                {{-- Stage 1 --}}
                <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        @if($resignation->notice_served)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Notice served</p>
                            @if($resignation->notice_served_date)
                                <p class="text-xs text-zinc-400">{{ $resignation->notice_served_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$resignation->notice_served)
                        <flux:button wire:click="markStage('notice_served')" size="sm" variant="ghost">Mark</flux:button>
                    @endif
                </div>

                {{-- Stage 2 --}}
                <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        @if($resignation->last_working_day_confirmed)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Last working day</p>
                            @if($resignation->last_working_day_confirmed_date)
                                <p class="text-xs text-zinc-400">{{ $resignation->last_working_day_confirmed_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$resignation->last_working_day_confirmed)
                        <flux:button wire:click="markStage('last_working_day')" size="sm" variant="ghost">Mark</flux:button>
                    @endif
                </div>

                {{-- Stage 3 --}}
                <div class="flex items-center justify-between py-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        @if($resignation->clearance_done)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Clearance done</p>
                            @if($resignation->clearance_done_date)
                                <p class="text-xs text-zinc-400">{{ $resignation->clearance_done_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$resignation->clearance_done)
                        <flux:button wire:click="markStage('clearance_done')" size="sm" variant="ghost">Mark</flux:button>
                    @endif
                </div>

                {{-- Stage 4 --}}
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        @if($resignation->final_pay_processed)
                            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <flux:icon.check class="w-3 h-3 text-green-600"/>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-200 dark:border-zinc-700"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Final pay processed</p>
                            @if($resignation->final_pay_processed_date)
                                <p class="text-xs text-zinc-400">{{ $resignation->final_pay_processed_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!$resignation->final_pay_processed)
                        <flux:button wire:click="markStage('final_pay_processed')" size="sm" variant="ghost">Mark</flux:button>
                    @endif
                </div>

                @if($resignation->status === 'completed')
                    <div class="mt-4 bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                        <flux:icon.check-circle class="w-6 h-6 text-green-500 mx-auto mb-1"/>
                        <p class="text-sm font-medium text-green-600">Resignation completed</p>
                    </div>
                @endif
            </flux:card>

            <flux:card>
                <p class="text-xs text-zinc-400">Recorded by {{ $resignation->recordedBy->name }}</p>
                <p class="text-xs text-zinc-400 mt-1">{{ $resignation->created_at->format('d M Y H:i') }}</p>
            </flux:card>
        </div>
    </div>
</div>