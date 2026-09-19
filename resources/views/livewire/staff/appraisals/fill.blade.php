

<div>
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('staff.appraisals.index') }}" icon="arrow-left" variant="ghost" size="sm"/>
        <div>
            <flux:heading size="xl">Fill appraisal</flux:heading>
            <flux:subheading>
                {{ $appraisal->employee->full_name }} ·
                {{ $appraisal->type_label }} · {{ $appraisal->period }}
            </flux:subheading>
        </div>
    </div>

    @if($appraisal->status === 'rejected' && $appraisal->hr_comments)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
            <flux:callout.heading>Returned by HR for revision</flux:callout.heading>
            <flux:callout.text>{{ $appraisal->hr_comments }}</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="submit">
        <flux:card class="mb-6">
            <flux:heading size="sm" class="mb-2">Rating scale</flux:heading>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                    <p class="font-medium text-green-700 dark:text-green-400">Exceeds expectations</p>
                    <p class="text-xs text-green-600 dark:text-green-500 mt-1">Score: 3 — Consistently surpasses requirements</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                    <p class="font-medium text-blue-700 dark:text-blue-400">Meets expectations</p>
                    <p class="text-xs text-blue-600 dark:text-blue-500 mt-1">Score: 2 — Meets all required standards</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                    <p class="font-medium text-red-700 dark:text-red-400">Below expectations</p>
                    <p class="text-xs text-red-600 dark:text-red-500 mt-1">Score: 1 — Does not meet required standards</p>
                </div>
            </div>
        </flux:card>

        <flux:card class="mb-6">
            <flux:heading size="sm" class="mb-4">KPI ratings</flux:heading>
            <div class="space-y-6">
                @foreach($appraisal->kpis as $kpi)
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                        <p class="font-medium text-zinc-800 dark:text-zinc-200 mb-3">
                            {{ $loop->iteration }}. {{ $kpi->kpi_label }}
                        </p>

                        {{-- Rating buttons --}}
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <button
                                type="button"
                                wire:click="$set('ratings.{{ $kpi->id }}', 'exceeds_expectations')"
                                class="py-2 px-3 rounded-lg border-2 text-sm font-medium transition-all
                                    {{ ($ratings[$kpi->id] ?? '') === 'exceeds_expectations'
                                        ? 'border-green-500 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:border-green-300' }}"
                            >
                                Exceeds
                            </button>
                            <button
                                type="button"
                                wire:click="$set('ratings.{{ $kpi->id }}', 'meets_expectations')"
                                class="py-2 px-3 rounded-lg border-2 text-sm font-medium transition-all
                                    {{ ($ratings[$kpi->id] ?? '') === 'meets_expectations'
                                        ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                        : 'border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:border-blue-300' }}"
                            >
                                Meets
                            </button>
                            <button
                                type="button"
                                wire:click="$set('ratings.{{ $kpi->id }}', 'below_expectations')"
                                class="py-2 px-3 rounded-lg border-2 text-sm font-medium transition-all
                                    {{ ($ratings[$kpi->id] ?? '') === 'below_expectations'
                                        ? 'border-red-500 bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                        : 'border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:border-red-300' }}"
                            >
                                Below
                            </button>
                        </div>

                        @error("ratings.{$kpi->id}")
                            <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                        @enderror

                        <flux:input
                            wire:model="comments.{{ $kpi->id }}"
                            placeholder="Comments (optional)…"
                            class="text-sm"
                        />
                    </div>
                @endforeach
            </div>
        </flux:card>

        <flux:card class="mb-6">
            <flux:heading size="sm" class="mb-4">Summary</flux:heading>
            <div class="space-y-4">
                <flux:textarea
                    wire:model="managerComments"
                    label="Overall comments"
                    placeholder="General comments on the employee's performance this period…"
                    rows="4"
                />
                <flux:textarea
                    wire:model="recommendations"
                    label="Recommendations & development areas"
                    placeholder="Areas for improvement, training recommendations, promotion considerations…"
                    rows="3"
                />
            </div>
        </flux:card>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('staff.appraisals.index') }}" variant="ghost">Cancel</flux:button>
            <flux:button
                type="submit"
                variant="primary"
                icon="paper-airplane"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Submit to HR</span>
                <span wire:loading>Submitting…</span>
            </flux:button>
        </div>
    </form>
</div>