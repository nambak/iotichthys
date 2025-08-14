<flux:modal name="add-user-modal" class="md:w-[500px]" @close="closeAddUserModal()">
    <flux:heading size="lg">{{ __('사용자 추가') }}</flux:heading>
    <flux:subheading>{{ $permission->name }} 권한에 사용자를 추가</flux:subheading>
    <div class="space-y-4">
        <flux:field>
            <div class="grid grid-cols-[7fr_1fr] gap-2">
                <flux:input
                        label=""
                        wire:model="email"
                        id="email"
                        class="w-full"
                        type="email"
                        placeholder="사용자의 이메일 주소를 입력해 주세요"
                />
                <flux:button variant="outline" class="w-fit mt-2" wire:click="searchUser">
                    {{ __('검색') }}
                </flux:button>
            </div>
        </flux:field>

        @if($foundUser)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <flux:heading size="sm" class="text-green-800 dark:text-green-200 mb-2">
                {{ __('사용자를 찾았습니다') }}
            </flux:heading>
            <div class="text-sm text-green-700 dark:text-green-300">
                <div><strong>이름:</strong> {{ $foundUser->name }}</div>
                <div><strong>이메일:</strong> {{ $foundUser->email }}</div>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <flux:button wire:click="addUserToPermission" variant="primary">
                {{ __('권한 추가') }}
            </flux:button>
        </div>
        @endif
    </div>
</flux:modal>
