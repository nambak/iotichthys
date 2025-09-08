<section class="w-full" x-data="organizationShow()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $organization->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">조직 상세 정보</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:modal.trigger name="add-user-modal">
                    <flux:button variant="primary" icon="plus">
                        {{ __('사용자 추가') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 조직 정보 카드 -->
    <livewire:organization.detail-card :organization="$organization" />

    <!-- 조직 구성원 리스트 -->
    <livewire:organization.user-list :organization="$organization" />

    <!-- 사용자 추가 모달 -->
    <livewire:organization.add-user-modal :organization="$organization"/>

</section>

<script>
    function organizationShow() {
        return {
            init() {
                this.$wire.on('user-added-to-organization', (event) => {
                    showSuccessToast(event[0].message);
                });

                this.$wire.on('user-removed-from-organization', (event) => {
                    showSuccessToast(event[0].message);
                });

                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event.message);
                });
            }
        }
    }
</script>