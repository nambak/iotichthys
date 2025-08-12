<flux:modal name="create-category" class="md:w-[500px]" @close="resetForm">
    <div>
        <flux:heading size="lg">
            {{ $parent_id ? '하위 카테고리 생성' : '카테고리 생성' }}
        </flux:heading>
    </div>
    <form wire:submit.prevent="create" class="font-siz0-[14px] mt-10">
        <div class="space-y-6">
            <flux:field>
                <flux:input
                        id="name"
                        label="카테고리 이름"
                        wire:model="name"
                        placeholder="카테고리 이름을 입력하세요"
                />
            </flux:field>
            <flux:field>
                <flux:textarea
                        id="description"
                        label="설명"
                        wire:model="description"
                        placeholder="카테고리에 대한 설명을 입력하세요"
                        rows="3"
                />
            </flux:field>
            <flux:field variant="inline" class="w-1/5">
                <flux:label for="is_active">활성화</flux:label>
                <flux:switch wire:model.live="is_active"/>
                <flux:error name="is_active"/>
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer/>
                <flux:button type="submit" variant="primary">
                    {{ $parent_id ? '하위 카테고리 생성' : '카테고리 생성' }}
                </flux:button>
            </div>
        </div>
    </form>
</flux:modal>