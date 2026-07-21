<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">User — Employee links</flux:heading>
            <flux:subheading>Link staff login accounts to employee records</flux:subheading>
        </div>
    </div>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if($unlinkedCount > 0)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
            <flux:callout.heading>{{ $unlinkedCount }} employee(s) have no login account</flux:callout.heading>
            <flux:callout.text>
                These staff cannot log in until linked.
                <button wire:click="$set('showUnlinked', true)" class="underline ml-1">
                    Show unlinked only
                </button>
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- Filters --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search name, staff no, email…"
                icon="magnifying-glass"
            />
            <div class="flex items-center gap-2">
                <flux:checkbox wire:model.live="showUnlinked" id="unlinked"/>
                <label for="unlinked" class="text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer">
                    Show unlinked only
                </label>
            </div>
        </div>
    </flux:card>

    {{-- Table --}}
    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Employee</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Staff no.</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Staff type</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Login account</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Role</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($employees as $employee)
                        <tr wire:key="{{ $employee->id }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $employee->full_name }}
                                </div>
                                <div class="text-xs text-zinc-400">{{ $employee->email }}</div>
                            </td>
                            <td class="py-3 px-3 font-mono text-xs text-zinc-500">
                                {{ $employee->staff_number }}
                            </td>
                            <td class="py-3 px-3 text-zinc-500">
                                {{ $employee->staff_type_label }}
                            </td>
                            <td class="py-3 px-3">
                                @if($employee->user)
                                    <div class="text-sm text-zinc-800 dark:text-zinc-200">
                                        {{ $employee->user->email }}
                                    </div>
                                    <flux:badge color="green" size="sm" class="mt-1">Linked</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">No account</flux:badge>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($employee->user)
                                    <flux:badge color="zinc" size="sm">
                                        {{ ucfirst(str_replace('_', ' ', $employee->user->role)) }}
                                    </flux:badge>
                                @else
                                    <span class="text-zinc-300">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    @if(!$employee->user)
                                        {{-- Create new login --}}
                                        <flux:button
                                            wire:click="openCreateModal({{ $employee->id }})"
                                            size="sm"
                                            variant="primary"
                                            icon="user-plus"
                                        >
                                            Create login
                                        </flux:button>
                                        {{-- Link to existing --}}
                                        <flux:button
                                            wire:click="openLinkModal({{ $employee->id }})"
                                            size="sm"
                                            variant="ghost"
                                            icon="link"
                                        >
                                            Link existing
                                        </flux:button>
                                    @else
                                        <flux:button
                                            wire:click="unlink({{ $employee->id }})"
                                            wire:confirm="Unlink {{ $employee->full_name }} from their login account?"
                                            size="sm"
                                            variant="ghost"
                                            icon="x-mark"
                                        >
                                            Unlink
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-zinc-400">
                                No employees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $employees->links() }}</div>
    </flux:card>

    {{-- Create login modal --}}
    <flux:modal wire:model="showCreateModal" class="max-w-md">
        <flux:heading size="lg">Create login account</flux:heading>
        <flux:subheading>
            Create a new user account and link it to this employee.
        </flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:input
                wire:model="createEmail"
                type="email"
                label="Email address"
                required
            />
            @error('createEmail')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <flux:input
                wire:model="createPassword"
                type="password"
                label="Password"
                description="Minimum 8 characters"
                required
            />
            @error('createPassword')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <flux:select wire:model="createRole" label="Role">
                <flux:select.option value="staff_admin">Staff</flux:select.option>
                <flux:select.option value="hr_admin">HR Admin</flux:select.option>
                <flux:select.option value="super_admin">Super Admin</flux:select.option>
                <flux:select.option value="teacher">Teacher</flux:select.option>
            </flux:select>

            <flux:callout variant="info" icon="information-circle">
                <flux:callout.text>
                    Share these credentials with the staff member securely.
                    They can change their password after first login.
                </flux:callout.text>
            </flux:callout>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button
                wire:click="$set('showCreateModal', false)"
                variant="ghost"
            >
                Cancel
            </flux:button>
            <flux:button
                wire:click="createAndLink"
                variant="primary"
                icon="user-plus"
            >
                Create & link
            </flux:button>
        </div>
    </flux:modal>

    {{-- Link to existing user modal --}}
    <flux:modal wire:model="showLinkModal" class="max-w-md">
        <flux:heading size="lg">Link to existing account</flux:heading>
        <flux:subheading>
            Select an existing user account to link to this employee.
            Only unlinked users are shown.
        </flux:subheading>

        <div class="mt-5">
            <flux:select wire:model="linkUserId" label="Select user" required>
                <flux:select.option value="">Choose a user…</flux:select.option>
                @foreach($availableUsers as $user)
                    <flux:select.option value="{{ $user->id }}">
                        {{ $user->name }} — {{ $user->email }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            @error('linkUserId')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:button
                wire:click="$set('showLinkModal', false)"
                variant="ghost"
            >
                Cancel
            </flux:button>
            <flux:button
                wire:click="linkToExisting"
                variant="primary"
                icon="link"
            >
                Link
            </flux:button>
        </div>
    </flux:modal>
</div>
