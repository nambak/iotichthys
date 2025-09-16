<section class="w-full" x-data="teamShow()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $team->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">팀 상세 정보</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:modal.trigger name="add-team-member-modal">
                    <flux:button variant="primary" icon="plus">
                        {{ __('팀 구성원 추가') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
        <flux:separator variant="subtle" />
    </div>

    <!-- 팀 정보 카드 -->
    <livewire:teams.detail-card :team="$team" />

    <!-- 팀 구성원 리스트 -->
    <livewire:teams.member-list :team="$team" />

    <!-- 팀 구성원 추가 모달 -->
    <livewire:teams.add-team-member-modal :team="$team" />
</section>

<script>
    function teamShow() {
        return {
            init() {
                this.$wire.on('user-added-to-team', (event) => {
                    showSuccessToast(event[0].message);
                });

                this.$wire.on('user-removed-from-team', (event) => {
                    showSuccessToast(event[0].message);
                });

                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event.message);
                });
            }
        }
    }
</script>