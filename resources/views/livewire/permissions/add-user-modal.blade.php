<div>
    <flux:modal name="add-user-modal" class="md:w-[500px]" @close="resetUserSearch">
        <div>
            <flux:heading size="lg">{{ __('사용자 추가') }}</flux:heading>
            <flux:subheading>{{ $permission->name }} 권한에 사용자를 추가합니다.</flux:subheading>
        </div>

        <form wire:submit.prevent="searchUser" class="mt-6">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="사용자 이메일"
                        placeholder="추가할 사용자의 이메일을 입력해 주세요"
                        wire:model="email"
                        type="email"
                    />
                </flux:field>

                @if($foundUser)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">찾은 사용자</flux:label>
                        <div class="mt-2 flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        {{ substr($foundUser->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $foundUser->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $foundUser->email }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex gap-2">
                    <flux:button type="submit" variant="outline">
                        {{ __('사용자 검색') }}
                    </flux:button>
                    
                    @if($canAddUser)
                        <flux:button 
                            type="button" 
                            variant="primary" 
                            wire:click="addUserToPermission"
                        >
                            {{ __('권한 추가') }}
                        </flux:button>
                    @endif
                    
                    <flux:spacer/>
                </div>
            </div>
        </form>
    </flux:modal>
</div>