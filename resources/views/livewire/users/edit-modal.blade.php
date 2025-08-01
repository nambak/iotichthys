<div>
    <flux:modal name="edit-user" class="md:w-96" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('사용자 정보 수정') }}</flux:heading>
        </div>
        <form wire:submit.prevent="update" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="이름"
                        placeholder="이름을 입력해 주세요"
                        wire:model.live="name"
                        required
                    />
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="이메일"
                        placeholder="이메일을 입력해 주세요"
                        wire:model="email"
                        type="email"
                        required
                    />
                </flux:field>
                
                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">{{ __('수정') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
