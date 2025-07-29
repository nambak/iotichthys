<div>
    <flux:modal name="edit-permission" class="md:w-96">
        <div>
            <flux:heading size="lg">{{ __('권한 수정') }}</flux:heading>
        </div>
        <form wire:submit.prevent="update" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="권한 이름"
                        placeholder="권한 이름을 입력해 주세요"
                        wire:model.live="name"
                        required
                    />
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="슬러그"
                        placeholder="권한 슬러그"
                        wire:model="slug"
                        required
                    />
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="리소스"
                        placeholder="리소스명 (예: device, organization, team)"
                        wire:model="resource"
                        required
                    />
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="액션"
                        placeholder="액션명 (예: create, read, update, delete)"
                        wire:model="action"
                        required
                    />
                </flux:field>
                
                <flux:field>
                    <flux:textarea
                        label="설명 (선택사항)"
                        placeholder="권한에 대한 설명을 입력해 주세요"
                        wire:model="description"
                        rows="3"
                    />
                </flux:field>
                
                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:button type="button" variant="ghost" wire:click="cancel">{{ __('취소') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('수정') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>