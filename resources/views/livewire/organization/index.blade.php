<section class="w-full">
    <div class="relative mb-3 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('조직 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-3">{{ __('조직을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- TODO: 조직 생성 권한 체크 -->
            <flux:button
                dusk="create-organization-button"
                variant="primary"
                icon="plus"
                wire:click="$dispatch('open-create-organization')"
            >
                {{ __('새 조직 추가') }}
            </flux:button>
        </div>
    </div>

    <!-- 조직 목록 테이블 -->
    <livewire:organization.list-table />

    <!-- 조직 모달 (생성/수정 통합) -->
    <livewire:organization.modal />
</section>
