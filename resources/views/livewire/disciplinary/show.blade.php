
<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('disciplinary.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Case {{ $disciplinaryCase->case_number }}</flux:heading>
            <flux:subheading>
                {{ $disciplinaryCase->employee->full_name }} ·
                {{ $disciplinaryCase->category_label }} ·
                {{ $disciplinaryCase->incident_date->format('d M Y') }}
            </flux:subheading>
        </div>
        <div class="ml-auto">
            <flux:badge color="{{ $disciplinaryCase->stage_color }}" size="sm">
                {{ $disciplinaryCase->stage_label }}
            </flux:badge>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left — details + document history --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Case summary --}}
            <flux:card>
                <flux:heading size="sm" class="mb-4">Case details</flux:heading>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-zinc-400">Employee</dt>
                        <dd class="font-medium mt-1">{{ $disciplinaryCase->employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Staff number</dt>
                        <dd class="font-mono mt-1">{{ $disciplinaryCase->employee->staff_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Division</dt>
                        <dd class="mt-1">{{ $disciplinaryCase->employee->division_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Branch</dt>
                        <dd class="mt-1">{{ $disciplinaryCase->employee->branch_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Category</dt>
                        <dd class="mt-1">{{ $disciplinaryCase->category_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Incident date</dt>
                        <dd class="mt-1">{{ $disciplinaryCase->incident_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Outcome</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $disciplinaryCase->outcome_color }}" size="sm">
                                {{ $disciplinaryCase->outcome_label }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-zinc-400">Description</dt>
                        <dd class="mt-1 text-zinc-600 dark:text-zinc-300">
                            {{ $disciplinaryCase->description }}
                        </dd>
                    </div>
                    @if($disciplinaryCase->outcome_notes)
                        <div class="col-span-2">
                            <dt class="text-zinc-400">Outcome notes</dt>
                            <dd class="mt-1 text-zinc-600 dark:text-zinc-300">
                                {{ $disciplinaryCase->outcome_notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </flux:card>

            {{-- Document history --}}
            <flux:card>
                <flux:heading size="sm" class="mb-4">Document history</flux:heading>

                @forelse($disciplinaryCase->documents->sortBy('document_date') as $doc)
                    <div class="flex items-start justify-between py-3 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                        <div class="flex items-start gap-3">
                            <flux:badge color="{{ $doc->type_color }}" size="sm" class="shrink-0 mt-0.5">
                                {{ $doc->type_label }}
                            </flux:badge>
                            <div>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $doc->type_label }}
                                    @if($doc->warning_level !== 'none')
                                        — {{ $doc->warning_level_label }}
                                    @endif
                                </p>
                                <p class="text-xs text-zinc-400 mt-0.5">
                                    {{ $doc->document_date->format('d M Y') }} ·
                                    {{ $doc->createdBy->name }}
                                </p>
                                @if($doc->type === 'response_to_show_cause')
                                    <p class="text-xs text-zinc-500 mt-1 italic">
                                        "{{ \Illuminate\Support\Str::limit($doc->content, 100) }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if(in_array($doc->type, ['show_cause_letter', 'warning_letter', 'hearing_record']))
                            <flux:button
                                href="{{ route('disciplinary.pdf', [$disciplinaryCase, $doc]) }}"
                                size="sm" icon="arrow-down-tray" variant="ghost"
                            >
                                PDF
                            </flux:button>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-zinc-400">No documents yet.</p>
                @endforelse
            </flux:card>
        </div>

        {{-- Right — action panel (stage-aware) --}}
        <div class="space-y-3">

            {{-- Stage tracker --}}
            <flux:card>
                <flux:heading size="sm" class="mb-4">Stage tracker</flux:heading>
                @php
                    $stages = [
                        'show_cause_issued' => 'Show cause issued',
                        'response_received' => 'Response received',
                        'hearing_held'      => 'Hearing held',
                        'outcome_recorded'  => 'Outcome recorded',
                        'warning_issued'    => 'Warning issued',
                        'closed'            => 'Closed',
                    ];
                    $stageKeys   = array_keys($stages);
                    $currentIdx  = array_search($disciplinaryCase->stage, $stageKeys);
                @endphp
                <div class="space-y-2">
                    @foreach($stages as $key => $label)
                        @php $idx = array_search($key, $stageKeys); @endphp
                        <div class="flex items-center gap-3">
                            @if($idx < $currentIdx)
                                <div class="w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                    <flux:icon.check class="w-3 h-3 text-green-600"/>
                                </div>
                            @elseif($idx === $currentIdx)
                                <div class="w-5 h-5 rounded-full bg-blue-500 shrink-0"></div>
                            @else
                                <div class="w-5 h-5 rounded-full border-2 border-zinc-200 dark:border-zinc-700 shrink-0"></div>
                            @endif
                            <span class="text-sm {{ $idx === $currentIdx ? 'font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-400' }}">
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </flux:card>

            {{-- Actions --}}
            <flux:card>
                <flux:heading size="sm" class="mb-3">Actions</flux:heading>
                <div class="space-y-2">

                    {{-- Show cause letter --}}
                    <flux:button
                        wire:click="$set('showShowCauseModal', true)"
                        variant="{{ $disciplinaryCase->showCauseLetter ? 'ghost' : 'primary' }}"
                        icon="document-text"
                        class="w-full"
                    >
                        {{ $disciplinaryCase->showCauseLetter ? 'Regenerate show cause' : 'Generate show cause letter' }}
                    </flux:button>

                    {{-- Record response --}}
                    @if(in_array($disciplinaryCase->stage, ['show_cause_issued','response_received','hearing_held','outcome_recorded','warning_issued']))
                        <flux:button
                            wire:click="$set('showResponseModal', true)"
                            variant="ghost"
                            icon="chat-bubble-left"
                            class="w-full"
                        >
                            Record employee response
                        </flux:button>
                    @endif

                    {{-- Hearing record --}}
                    @if(in_array($disciplinaryCase->stage, ['response_received','hearing_held','outcome_recorded','warning_issued']))
                        <flux:button
                            wire:click="$set('showHearingModal', true)"
                            variant="ghost"
                            icon="clipboard-document"
                            class="w-full"
                        >
                            {{ $disciplinaryCase->hearingRecord ? 'Update hearing record' : 'Record hearing' }}
                        </flux:button>
                    @endif

                    {{-- Outcome --}}
                    @if(in_array($disciplinaryCase->stage, ['hearing_held','outcome_recorded','warning_issued']) && $disciplinaryCase->outcome === 'pending')
                        <flux:button
                            wire:click="$set('showOutcomeModal', true)"
                            variant="ghost"
                            icon="check-badge"
                            class="w-full"
                        >
                            Record outcome
                        </flux:button>
                    @endif

                    {{-- Warning letter --}}
                    @if(in_array($disciplinaryCase->stage, ['outcome_recorded','warning_issued']) && $disciplinaryCase->outcome !== 'pending' && $disciplinaryCase->outcome !== 'no_action')
                        <flux:button
                            wire:click="$set('showWarningModal', true)"
                            variant="{{ $disciplinaryCase->warningLetter ? 'ghost' : 'primary' }}"
                            icon="exclamation-triangle"
                            class="w-full"
                        >
                            {{ $disciplinaryCase->warningLetter ? 'Regenerate warning letter' : 'Generate warning letter' }}
                        </flux:button>
                    @endif

                    {{-- Close case --}}
                    @if($disciplinaryCase->stage !== 'closed')
                        <flux:button
                            wire:click="closeCase"
                            wire:confirm="Close this disciplinary case?"
                            variant="ghost"
                            icon="x-circle"
                            class="w-full"
                        >
                            Close case
                        </flux:button>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>

    {{-- Show cause modal --}}
    <flux:modal wire:model="showShowCauseModal" class="max-w-2xl">
        <flux:heading size="lg">Generate show cause letter</flux:heading>
        <flux:subheading>The letter will be pre-filled with the case description.</flux:subheading>
        <div class="mt-5 space-y-4">
            <flux:input wire:model="showCauseDate" type="date" label="Letter date" required/>
            <flux:textarea wire:model="showCauseContent" label="Additional content (optional)"
                placeholder="Any additional content to include in the letter beyond the incident description…" rows="4"/>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showShowCauseModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="saveShowCause" variant="primary" icon="document-text">
                Generate letter
            </flux:button>
        </div>
    </flux:modal>

    {{-- Response modal --}}
    <flux:modal wire:model="showResponseModal" class="max-w-xl">
        <flux:heading size="lg">Record employee response</flux:heading>
        <flux:subheading>Record the employee's written response to the show cause letter.</flux:subheading>
        <div class="mt-5 space-y-4">
            <flux:input wire:model="responseDate" type="date" label="Response date" required/>
            <flux:textarea wire:model="responseText" label="Employee response" rows="5" required
                placeholder="Record the employee's response verbatim or summarised…"/>
            @error('responseText')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showResponseModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="saveResponse" variant="primary" icon="check">Save response</flux:button>
        </div>
    </flux:modal>

    {{-- Hearing modal --}}
    <flux:modal wire:model="showHearingModal" class="max-w-2xl">
        <flux:heading size="lg">Record disciplinary hearing</flux:heading>
        <flux:subheading>Document the hearing proceedings and any decisions made.</flux:subheading>
        <div class="mt-5 space-y-4">
            <flux:input wire:model="hearingDate" type="date" label="Hearing date" required/>
            <flux:textarea wire:model="hearingNotes" label="Hearing notes" rows="6" required
                placeholder="Record the hearing proceedings, attendees, statements made, and any immediate decisions…"/>
            @error('hearingNotes')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showHearingModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="saveHearing" variant="primary" icon="clipboard-document">
                Save hearing record
            </flux:button>
        </div>
    </flux:modal>

    {{-- Outcome modal --}}
    <flux:modal wire:model="showOutcomeModal" class="max-w-lg">
        <flux:heading size="lg">Record outcome</flux:heading>
        <flux:subheading>Record the final disciplinary outcome for this case.</flux:subheading>
        <div class="mt-5 space-y-4">
            <flux:select wire:model="outcome" label="Outcome" required>
                <flux:select.option value="">Select outcome…</flux:select.option>
                <flux:select.option value="verbal_warning">Verbal warning</flux:select.option>
                <flux:select.option value="written_warning">Written warning</flux:select.option>
                <flux:select.option value="final_written_warning">Final written warning</flux:select.option>
                <flux:select.option value="dismissal">Dismissal</flux:select.option>
                <flux:select.option value="no_action">No action taken</flux:select.option>
            </flux:select>
            <flux:input wire:model="outcomeDate" type="date" label="Outcome date" required/>
            <flux:textarea wire:model="outcomeNotes" label="Notes" rows="3"
                placeholder="Any additional notes about the outcome…"/>
            @error('outcome')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showOutcomeModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="saveOutcome" variant="primary" icon="check-badge">
                Save outcome
            </flux:button>
        </div>
    </flux:modal>

    {{-- Warning letter modal --}}
    <flux:modal wire:model="showWarningModal" class="max-w-xl">
        <flux:heading size="lg">Generate warning letter</flux:heading>
        <div class="mt-5 space-y-4">
            <flux:input wire:model="warningDate" type="date" label="Letter date" required/>
            <flux:select wire:model="warningLevel" label="Warning level" required>
                <flux:select.option value="">Select level…</flux:select.option>
                <flux:select.option value="verbal">Verbal warning</flux:select.option>
                <flux:select.option value="written">Written warning</flux:select.option>
                <flux:select.option value="final_written">Final written warning</flux:select.option>
                <flux:select.option value="dismissal">Dismissal</flux:select.option>
            </flux:select>
            <flux:textarea wire:model="warningContent" label="Additional content (optional)"
                placeholder="Any additional content beyond the standard warning text…" rows="3"/>
            @error('warningLevel')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button wire:click="$set('showWarningModal', false)" variant="ghost">Cancel</flux:button>
            <flux:button wire:click="saveWarningLetter" variant="primary" icon="exclamation-triangle">
                Generate letter
            </flux:button>
        </div>
    </flux:modal>
</div>
