<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">User Management</flux:heading>
            <flux:subheading>Edit Users in the system and their Roles</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif



    {{-- Filters --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search name" icon="magnifying-glass" />
        </div>
    </flux:card>

    {{-- Table --}}
    <flux:card class="mt-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Name</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Email</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Role</th>
                        <th class="text-left py-3 px-3 text-zinc-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($users as $user)
                        <tr wire:key="{{ $user->id }}">
                            <td class="py-3 px-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="text-xs text-zinc-400">{{ $user->email }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <flux:badge color="zinc" size="sm">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </flux:badge>

                            </td>

                            <td class="py-3 px-3">
                                <flux:button wire:click="openEditModal({{ $user->id }})" size="sm"
                                    variant="ghost" icon="pencil-square"></flux:button>
                            </td>

                            @if(in_array($user->role, ['teacher', 'staff_admin', 'admin']))
                            <td class="py-3 px-3">
                            
                                  <flux:button  e="sm" wire:click='openDeleteModal'
                                    variant="ghost" icon="trash"></flux:button>
                            
                                {{-- <flux:button wire:click="deleteUser({{ $user->id }})" 
                                wire:confirm='Are you Sure you want to delete {{ $user->name }}' size="sm"
                                    variant="ghost" icon="trash"></flux:button> --}}
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-zinc-400">
                                No Users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </flux:card>

    <flux:modal wire:model="showEditModal" class="max-w-md">

        <flux:heading size="lg">Edit User Account</flux:heading>
        <flux:subheading>
            Edit a User Account Details
        </flux:subheading>
        <form wire:submit='editUser'>
            <div class="mt-5 space-y-4">
                <flux:input wire:model="userName" type="text" label="User Name" required />
                @error('userName')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror

                <flux:input wire:model="userEmail" type="email" label="Email" required />
                @error('userEmail')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror



                <flux:select wire:model.live="userRole" label="Role">
                    <flux:select.option value="staff_admin">Staff</flux:select.option>
                    <flux:select.option value="hr_admin">HR Admin</flux:select.option>
                    <flux:select.option value="super_admin">Super Admin</flux:select.option>
                    <flux:select.option value="teacher">Teacher</flux:select.option>
                </flux:select>

                @error('userRole')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror

            </div>

            <div class="flex justify-end gap-3 mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" icon="" type="submit">
                    Edit
                </flux:button>
            </div>

        </form>


    </flux:modal>

    <flux:modal wire:model='showDeleteModal' class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Delete User?</flux:heading>
            <flux:text class="mt-2">
                You're about to delete this user.<br>
                This action cannot be reversed.
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <!-- Close Button -->
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>

            <!-- Action Button -->
            <flux:button wire:click="deleteUser({{ $user->id }})" variant="danger">
                Delete
            </flux:button>
        </div>
    </div>
</flux:modal>
