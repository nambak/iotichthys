<section class="w-full" x-data="organizationShow()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $organization->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">조직 상세 정보</flux:subheading>
            </div>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 조직 정보 카드 -->
    <livewire:organization.detail-card :organization="$organization" />

    <div class="text-sm font-medium text-center border-b text-gray-400 border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="me-2">
                <a href="#"
                   @click.prevent="activeTab = 'users'"
                   :class="{
                       'text-white border-white': activeTab === 'users',
                       'border-transparent hover:border-gray-300 hover:text-gray-300': activeTab !== 'users'
                   }"
                   class="inline-block p-4 border-b-2 rounded-t-lg transition-colors">
                    조직 구성원
                </a>
            </li>
            <li class="me-2">
                <a href="#"
                   @click.prevent="activeTab = 'teams'"
                   :class="{
                       'text-white border-white': activeTab === 'teams',
                       'border-transparent hover:border-gray-300 hover:text-gray-300': activeTab !== 'teams'
                   }"
                   class="inline-block p-4 border-b-2 rounded-t-lg transition-colors">
                    팀 목록
                </a>
            </li>
        </ul>
    </div>

    <!-- 조직 구성원 리스트 -->
    <div x-show="activeTab === 'users'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
    >
        <livewire:organization.user-list :organization="$organization" />
    </div>

    <!-- 팀 목록 -->
    <div x-show="activeTab === 'teams'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
    >
        <livewire:teams.list-table
            :organization="$organization"
            :show-organization-column="false"
            :show-pagination="false"
        />
    </div>

    <!-- 사용자 추가 모달 -->
    <livewire:organization.add-user-modal :organization="$organization"/>

</section>

<script>
    function organizationShow() {
        return {
            activeTab: 'users',

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