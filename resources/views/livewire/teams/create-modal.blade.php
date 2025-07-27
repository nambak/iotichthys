<div x-data="createTeamModal()">
    <flux:modal name="create-team" class="md:w-96" @close="resetForm">
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
                            <flux:option value="{{ $organization->id }}">{{ $organization->name }}</flux:option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="팀명"
                        placeholder="팀명을 입력해 주세요"
                        wire:model.live="name"
                    />
                </flux:field>
                
                <flux:field>
                    <flux:input
                        label="팀 식별자"
                        placeholder="팀 식별자가 자동으로 생성됩니다"
                        wire:model="slug"
                        description="영어 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다"
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

<script>
    function createTeamModal() {
        return {
            // 추가 JavaScript 로직이 필요한 경우 여기에 구현
        }
    }
</script>