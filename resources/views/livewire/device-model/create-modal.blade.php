<div>
    <flux:modal name="create-device-model" class="w-1/2" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('새 장치 모델 추가') }}</flux:heading>
        </div>
        <form wire:submit.prevent="save" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        label="모델명"
                        placeholder="장치 모델명을 입력해 주세요"
                        wire:model="name"
                    />
                </flux:field>

                <flux:field>
                    <flux:textarea
                        label="사양"
                        placeholder="장치 사양을 줄바꿈으로 구분하여 입력해 주세요 (선택사항)"
                        wire:model="specifications"
                        rows="4"
                    />
                    <flux:description>
                        예: 온도 센서 (0~100°C)<br>
                        습도 센서 (0~100%)<br>
                        WiFi 연결 지원
                    </flux:description>
                </flux:field>

                <flux:field>
                    <flux:textarea
                        label="설명"
                        placeholder="장치 모델에 대한 설명을 입력해 주세요 (선택사항)"
                        wire:model="description"
                        rows="3"
                    />
                </flux:field>

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">
                        {{ __('모델 추가') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>