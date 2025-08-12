<flux:modal name="edit-category" class="md:w-[500px]" @close="resetForm">
    <div>
        <flux:heading size="lg">
            카테고리 수정
        </flux:heading>
    </div>
    <form wire:submit.prevent="update" class="font-size-[14px] mt-10">
        <div class="space-y-6">
            <flux:field>
                <flux:input
                        id="edit_name"
                        label="카테고리 이름"
                        wire:model="name"
                        placeholder="카테고리 이름을 입력하세요"
                />
            </flux:field>
            <flux:field>
                <flux:textarea
                        id="edit_description"
                        label="설명"
                        wire:model="description"
                        placeholder="카테고리에 대한 설명을 입력하세요"
                        rows="3"
                />
            </flux:field>
            <flux:field variant="inline" class="w-1/5">
                <flux:label for="edit_is_active">활성화</flux:label>
                <flux:switch wire:model.live="is_active"/>
                <flux:error name="is_active"/>
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer/>
                <flux:button type="button" variant="ghost" @click="$dispatch('modal-close', { modal: 'edit-category' })">
                    취소
                </flux:button>
                <flux:button type="submit" variant="primary">
                    카테고리 수정
                </flux:button>
            </div>
        </div>
    </form>
</flux:modal>