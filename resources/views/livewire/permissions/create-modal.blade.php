<div>
    <flux:modal name="create-permission" class="md:w-[500px]" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('새 권한 생성') }}</flux:heading>
        </div>
        <form wire:submit.prevent="create" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="권한 이름"
                        placeholder="권한 이름을 입력해 주세요"
                        wire:model="name"
                    />
                </flux:field>

                <flux:field>
                    <flux:input
                        label="리소스"
                        placeholder="리소스명 (예: device, organization, team)"
                        wire:model="resource"
                    />
                </flux:field>

                <flux:field>
                    <flux:input
                        label="액션"
                        placeholder="액션명 (예: create, read, update, delete)"
                        wire:model="action"
                    />
                </flux:field>

                <flux:field>
                    <flux:textarea
                        label="설명 (선택사항)"
                        placeholder="권한에 대한 설명을 입력해 주세요"
                        wire:model="description"
                    />
                </flux:field>

                <flux:field>
                    <flux:label>사용자 할당 (선택사항)</flux:label>
                    <div class="mt-2 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2">
                        @forelse($users as $user)
                            <label class="flex items-center space-x-2 py-1">
                                <input 
                                    type="checkbox" 
                                    wire:model="selectedUsers" 
                                    value="{{ $user->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $user->name }} ({{ $user->email }})
                                </span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">사용 가능한 사용자가 없습니다.</p>
                        @endforelse
                    </div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        선택된 사용자들에게 이 권한이 자동으로 할당됩니다.
                    </div>
                </flux:field>

                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">{{ __('저장') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>