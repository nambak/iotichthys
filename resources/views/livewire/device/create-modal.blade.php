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

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-800 dark:text-white">장치 모델</label>
                    <select wire:model="device_model_id" class="w-full px-3 py-2 border border-zinc-300 rounded-md dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                        <option value="">장치 모델을 선택해 주세요</option>
                        @foreach($deviceModels as $model)
                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-800 dark:text-white">장치 상태</label>
                    <select wire:model="status" class="w-full px-3 py-2 border border-zinc-300 rounded-md dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                        <option value="active">활성</option>
                        <option value="inactive">비활성</option>
                        <option value="maintenance">점검중</option>
                        <option value="error">오류</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-800 dark:text-white">소속 조직</label>
                    <select wire:model="organization_id" class="w-full px-3 py-2 border border-zinc-300 rounded-md dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                        <option value="">조직 없음</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>

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
