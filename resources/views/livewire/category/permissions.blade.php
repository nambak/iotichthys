<div class="space-y-6" x-data="categoryPermissions()">
    <!-- 권한 보유자 목록 -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">권한 보유자</flux:heading>
            <flux:modal.trigger name="add-permission-user-modal">
                <flux:button variant="primary" icon="user-plus">
                    사용자 추가
                </flux:button>
            </flux:modal.trigger>
        </div>

        @if($authorizedUsers->count() > 0)
        <div class="grid gap-3">
            @foreach($authorizedUsers as $user)
            <div class="flex items-center justify-between p-4 border border-zinc-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600">
                                    {{ $user->initials() }}
                                </span>
                    </div>
                    <div>
                        <flux:heading size="sm">{{ $user->name }}</flux:heading>
                        <flux:subheading size="sm" class="text-gray-600">{{ $user->email }}</flux:subheading>
                    </div>
                </div>
                <div>
                    <flux:button
                            size="sm"
                            variant="danger"
                            icon="user-minus"
                            x-on:click="confirmRevoke({{ $user->id }}, '{{ $user->name }}')"
                    >
                        권한 취소
                    </flux:button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-zinc-800 border border-zinc-700 rounded-lg">
            <flux:icon.users class="mx-auto h-12 w-12 text-gray-400 mb-4"/>
            <flux:subheading>권한을 가진 사용자가 없습니다</flux:subheading>
            <p class="text-sm text-gray-500 mt-2">위의 "사용자 추가" 버튼을 클릭해서 사용자에게 권한을 부여할 수 있습니다.</p>
        </div>
        @endif
    </div>

    <!-- 사용자 추가 모달 -->
    <livewire:category.add-user-modal :category="$category"/>
</div>

<script>
    function categoryPermissions() {
        return {
            confirmRevoke(userId, userName) {
                confirmDelete(
                    `정말로 ${userName}님의 권한을 취소하시겠습니까?`,
                    () => {
                        this.$wire.revokeAccess(userId);
                    }
                );
            },

            init() {
                this.$wire.on('show-success-toast', (event) => {
                    showSuccessToast(event[0].message);
                });

                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event[0].message);
                });
            }
        }
    }
</script>
