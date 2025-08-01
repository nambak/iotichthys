<div>
    <flux:modal name="create-permission" class="md:w-96" @close="resetForm">
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

                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">{{ __('저장') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>