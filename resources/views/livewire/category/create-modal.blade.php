<flux:modal name="create-category" class="md:w-96">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <flux:heading size="lg">
                {{ $parent_id ? '하위 카테고리 생성' : '카테고리 생성' }}
            </flux:heading>
        </div>

        <form wire:submit="create">
            <div class="space-y-6">
                <flux:field>
                    <flux:label for="name">카테고리 이름 *</flux:label>
                    <flux:input 
                        id="name" 
                        wire:model="name" 
                        placeholder="카테고리 이름을 입력하세요"
                        required
                    />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label for="description">설명</flux:label>
                    <flux:textarea 
                        id="description" 
                        wire:model="description" 
                        placeholder="카테고리에 대한 설명을 입력하세요"
                        rows="3"
                    />
                    <flux:error name="description" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label for="sort_order">정렬 순서</flux:label>
                        <flux:input 
                            id="sort_order" 
                            type="number" 
                            wire:model="sort_order" 
                            min="0"
                            placeholder="0"
                        />
                        <flux:error name="sort_order" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="is_active">상태</flux:label>
                        <flux:select wire:model="is_active">
                            <flux:option value="1">활성</flux:option>
                            <flux:option value="0">비활성</flux:option>
                        </flux:select>
                        <flux:error name="is_active" />
                    </flux:field>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost">취소</flux:button>
                </flux:modal.close>
                
                <flux:button type="submit" variant="primary">
                    {{ $parent_id ? '하위 카테고리 생성' : '카테고리 생성' }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>