<div x-data="editDeviceModal()">
    <flux:modal name="edit-device" class="w-1/2" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('장치 수정') }}</flux:heading>
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

                <flux:select
                    label="제조사 필터 (선택사항)"
                    placeholder="제조사를 선택하면 해당 제조사의 모델만 표시됩니다"
                    wire:model.live="manufacturerFilter"
                >
                    <flux:select.option value="">전체 제조사</flux:select.option>
                    @foreach($manufacturers as $manufacturer)
                        <flux:select.option value="{{ $manufacturer }}">{{ $manufacturer }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:field>
                    <flux:select
                        label="장치 모델"
                        wire:model="device_model_id"
                    >
                        @foreach($deviceModels as $model)
                            <flux:select.option value="{{ $model->id }}">
                                {{ $model->name }}@if($model->manufacturer) ({{ $model->manufacturer }})@endif
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:select
                       label="장치 상태"
                       wire:model="status"
                    >
                        <flux:select.option value="active">활성</flux:select.option>
                        <flux:select.option value="inactive">비활성</flux:select.option>
                        <flux:select.option value="maintenance">점검중</flux:select.option>
                        <flux:select.option value="error">오류</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:select
                        label="소속 조직"
                        wire:model="organization_id"
                    >
                        @foreach($organizations as $organization)
                            <flux:select.option value="{{ $organization->id }}">
                                {{ $organization->name }}
                            </flux:select.option>
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

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">
                        {{ __('수정 완료') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>

<script>
function editDeviceModal() {
    return {
        resetForm() {
            @this.call('resetForm');
        }
    }
}
</script>
