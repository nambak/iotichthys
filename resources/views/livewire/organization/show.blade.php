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
                <flux:button href="{{ route('organization.index') }}" variant="outline">
                    목록으로
                </flux:button>
            </div>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 조직 정보 카드 -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 mb-6">
        <flux:heading size="lg" class="mb-4">조직 정보</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <flux:field>
                    <flux:label>조직 이름</flux:label>
                    <flux:input value="{{ $organization->name }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>대표자</flux:label>
                    <flux:input value="{{ $organization->owner }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>사업자 번호</flux:label>
                    <flux:input value="{{ $organization->business_register_number }}" readonly />
                </flux:field>
            </div>
            <div>
                <flux:field>
                    <flux:label>대표 전화</flux:label>
                    <flux:input value="{{ $organization->phone_number }}" readonly />
                </flux:field>
            </div>
            <div class="md:col-span-2">
                <flux:field>
                    <flux:label>주소</flux:label>
                    <flux:input value="{{ $organization->address }} {{ $organization->detail_address ?? '' }}" readonly />
                </flux:field>
            </div>
            @if($organization->postcode)
            <div>
                <flux:field>
                    <flux:label>우편번호</flux:label>
                    <flux:input value="{{ $organization->postcode }}" readonly />
                </flux:field>
            </div>
            @endif
            <div>
                <flux:field>
                    <flux:label>등록일</flux:label>
                    <flux:input value="{{ $organization->created_at->format('Y년 m월 d일') }}" readonly />
                </flux:field>
            </div>
        </div>
    </div>

    <!-- 조직 구성원 리스트 -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <flux:heading size="lg">조직 구성원 ({{ $organization->users()->count() }}명)</flux:heading>
        </div>

        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                                이름
                            </th>
                            <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                                이메일
                            </th>
                            <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                                역할
                            </th>
                            <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                                가입일
                            </th>
                            <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
                        @foreach ($users as $user)
                        <tr>
                            <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                                {{ $user->name }}
                            </td>
                            <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                                {{ $user->email }}
                            </td>
                            <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                                @if($user->pivot->is_owner)
                                    <flux:badge color="amber">소유자</flux:badge>
                                @else
                                    <flux:badge color="zinc">구성원</flux:badge>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                                {{ $user->pivot->created_at->format('Y-m-d') }}
                            </td>
                            <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                                @if(!$user->pivot->is_owner)
                                    <flux:icon.trash
                                        class="size-4 hover:text-red-600 transition-colors cursor-pointer"
                                        @click="removeUser({{ $user->id }})"
                                    />
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
                {{ $users->links('custom-flux-pagination') }}
            </div>
        @else
            <div class="text-center py-8">
                <flux:icon.users class="mx-auto h-12 w-12 text-gray-400" />
                <flux:heading size="md" class="mt-2 text-gray-600 dark:text-gray-400">조직에 속한 구성원이 없습니다</flux:heading>
                <flux:subheading class="mt-1 text-gray-500">새로운 구성원을 추가해보세요.</flux:subheading>
            </div>
        @endif
    </div>

    <!-- 사용자 추가 모달 -->
    <livewire:organization.add-user-modal :organization="$organization"/>

</section>

<script>
    function organizationShow() {
        return {
            removeUser(userId) {
                confirmDelete('정말로 이 사용자를 조직에서 제거하시겠습니까?', () => {
                    this.$wire.removeUserFromOrganization(userId);
                });
            },

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