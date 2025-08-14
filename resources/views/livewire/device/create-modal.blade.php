<div x-data="createDeviceModal()">
    <flux:modal name="create-device" class="md:w-96" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('새 장치 추가') }}</flux:heading>
        </div>
        <form wire:submit.prevent="save" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="장치명"
                        placeholder="장치명을 입력해 주세요"
                        wire:model="name"
                    />
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="장치 ID"
                        placeholder="고유한 장치 ID를 입력해 주세요"
                        wire:model="device_id"
                    />
                </flux:field>

                <flux:field>
                    <flux:select 
                        label="장치 모델" 
                        placeholder="장치 모델을 선택해 주세요"
                        wire:model="device_model_id"
                    >
                        @foreach($deviceModels as $model)
                            <flux:option value="{{ $model->id }}">{{ $model->name }}</flux:option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:select 
                        label="장치 상태" 
                        wire:model="status"
                    >
                        <flux:option value="active">활성</flux:option>
                        <flux:option value="inactive">비활성</flux:option>
                        <flux:option value="maintenance">점검중</flux:option>
                        <flux:option value="error">오류</flux:option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:select 
                        label="소속 조직" 
                        placeholder="조직을 선택해 주세요 (선택사항)"
                        wire:model="organization_id"
                    >
                        <flux:option value="">조직 없음</flux:option>
                        @foreach($organizations as $organization)
                            <flux:option value="{{ $organization->id }}">{{ $organization->name }}</flux:option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:input
                        label="위치"
                        placeholder="장치 설치 위치를 입력해 주세요 (선택사항)"
                        wire:model="location"
                    />
                </flux:field>

                <flux:field>
                    <flux:textarea
                        label="설명"
                        placeholder="장치에 대한 설명을 입력해 주세요 (선택사항)"
                        wire:model="description"
                        rows="3"
                    />
                </flux:field>
            </div>

            <div class="flex space-x-3 mt-6">
                <flux:button type="submit" variant="primary">
                    {{ __('장치 추가') }}
                </flux:button>
                <flux:modal.close>
                    <flux:button variant="ghost">
                        {{ __('취소') }}
                    </flux:button>
                </flux:modal.close>
            </div>
        </form>
    </flux:modal>
</div>

<script>
function createDeviceModal() {
    return {
        resetForm() {
            @this.call('resetForm');
        }
    }
}
</script>
