<section class="w-full" x-data="permissionShow()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1">{{ $permission->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ $permission->description ?: '권한 상세 정보 및 사용자 관리' }}</flux:subheading>
            </div>

            <flux:modal.trigger name="add-user-modal">
                <flux:button dusk="add-user-button" variant="primary" icon="plus">
                    {{ __('사용자 추가') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 권한 정보 카드 -->
    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <flux:label class="text-sm font-medium text-gray-500 dark:text-gray-400">리소스</flux:label>
                <div class="mt-1">
                    <span class="inline-flex px-2 py-1 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                        {{ $permission->resource }}
                    </span>
                </div>
            </div>
            <div>
                <flux:label class="text-sm font-medium text-gray-500 dark:text-gray-400">액션</flux:label>
                <div class="mt-1">
                    <span class="inline-flex px-2 py-1 text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                        {{ $permission->action }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 사용자 목록 -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <flux:heading size="lg">권한을 가진 사용자 ({{ $users->total() }}명)</flux:heading>
        </div>
        
        @if($users->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($users as $user)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <flux:icon.trash
                                class="size-4 text-gray-400 hover:text-red-600 transition-colors cursor-pointer"
                                @click="removeUser({{ $user->id }})"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- 페이지네이션 -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="text-xs text-zinc-500 dark:text-zinc-300">
                    {{ $users->links('custom-flux-pagination') }}
                </div>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <flux:icon.users class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">사용자 없음</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">이 권한을 가진 사용자가 없습니다.</p>
                <div class="mt-6">
                    <flux:modal.trigger name="add-user-modal">
                        <flux:button variant="primary" icon="plus">
                            {{ __('첫 번째 사용자 추가') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        @endif
    </div>

    <!-- 사용자 추가 모달 -->
    <livewire:permissions.add-user-modal :permission="$permission" />
</section>

<script>
    function permissionShow() {
        return {
            removeUser(userId) {
                confirmDelete('정말로 이 사용자의 권한을 제거하시겠습니까?', () => {
                    this.$wire.removeUserFromPermission(userId);
                });
            },

            init() {
                this.$wire.on('user-removed-from-permission', (event) => {
                    showSuccessToast(event[0].message);
                });

                this.$wire.on('user-added-to-permission', (event) => {
                    showSuccessToast(event[0].message);
                });
            }
        }
    }
</script>