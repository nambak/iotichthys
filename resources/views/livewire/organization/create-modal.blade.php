<flux:modal name="create-organization" class="md:w-96" @close="resetForm">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('새 조직 추가') }}</flux:heading>
        </div>
        <form wire:submit.prevent="save" class="font-size-[14px]">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="사업자명"
                        placeholder="사업자명을 입력해 주세요"
                        wire:model="name"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                        label="대표자 명"
                        placeholder="대표자 명을 입력해 주세요"
                        wire:model="owner"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                        label="사업자 번호"
                        placeholder="-을 제외한 사업자 번호를 입력해 주세요"
                        wire:model="businessRegisterNumber"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                        label="사업장 주소"
                        placeholder="사업장 주소를 입력해 주세요"
                        wire:model="address"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                        label="사업자 전화번호"
                        placeholder="-를 제외한 대표 전화 번호를 입력해 주세요 "
                        wire:model="phoneNumber"
                    />
                </flux:field>
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">{{ __('저장') }}</flux:button>
                </div>
            </div>
        </form>
    </div>
</flux:modal>
