<div>
    <flux:modal name="create-team" class="w-1/2" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('새 팀 생성') }}</flux:heading>
        </div>
        <form wire:submit.prevent="save" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:select
                label="소속 조직"
                        placeholder="조직을 선택해 주세요"
                        wire:model="organization_id"
                    >
                        @foreach($organizations as $organization)
                            <flux:select.option value="{{ $organization->id }}">{{ $organization->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="팀명"
                        placeholder="팀명을 입력해 주세요"
                        wire:model="name"
                    />
                </flux:field>
                
                <flux:field>
                    <flux:textarea
                        label="팀 설명 (선택사항)"
                        placeholder="팀에 대한 설명을 입력해 주세요"
                        wire:model="description"
                        rows="3"
                    />
                </flux:field>
                
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">{{ __('저장') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
