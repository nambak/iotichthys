<div x-data="editTeamModal()">
    <flux:modal name="edit-team" class="md:w-96" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('팀 수정') }}</flux:heading>
        </div>
        <form wire:submit.prevent="update" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                            label="팀 이름"
                            placeholder="팀 이름을 입력해 주세요"
                            wire:model="name"
                    />
                </flux:field>
                <flux:field>
                    <flux:select
                        label="소속 조직"
                        placeholder="조직을 선택해 주세요"
                        wire:model="organization_id"
                    >
                        @forelse($organizations as $organization)
                            <flux:select.option value="{{ $organization->id }}">
                                {{ $organization->name }}
                            </flux:select.option>
                        @empty
                            <flux:select.option>등록된 조직이 없습니다. 조직 등록을 먼저 해주세요.</flux:select.option>
                        @endforelse
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:textarea
                            label="설명"
                            placeholder="팀 설명을 입력해 주세요"
                            wire:model="description"
                            rows="4"
                    />
                </flux:field>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">{{ __('수정') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>

<script>
    function editTeamModal() {
        return {
            resetForm() {
                // 모달이 닫힐 때 폼 리셋
                if (this.$wire) {
                    this.$wire.resetForm();
                }
            }
        }
    }
</script>