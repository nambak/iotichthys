<div class="space-y-6" x-data="categoryPermissions()">
    <!-- 권한 보유자 목록 -->
    <div>
        <flux:heading size="lg" class="mb-4">권한 보유자</flux:heading>
        
        @if($authorizedUsers->count() > 0)
            <div class="grid gap-3">
                @foreach($authorizedUsers as $user)
                    <div class="flex items-center justify-between p-4 bg-white border rounded-lg">
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
                                wire:click="revokeAccess({{ $user->id }})"
                                x-on:click="confirmRevoke({{ $user->id }}, '{{ $user->name }}')"
                            >
                                권한 취소
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <flux:icon.users class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <flux:subheading>권한을 가진 사용자가 없습니다</flux:subheading>
                <p class="text-sm text-gray-500 mt-2">아래에서 사용자에게 권한을 부여할 수 있습니다.</p>
            </div>
        @endif
    </div>

    <!-- 권한 부여 가능한 사용자들 -->
    <div>
        <flux:heading size="lg" class="mb-4">권한 부여</flux:heading>
        
        <!-- 검색 입력 -->
        <div class="mb-4">
            <flux:input 
                wire:model.live.debounce.500ms="searchTerm"
                placeholder="사용자 이름 또는 이메일로 검색..."
                icon="magnifying-glass"
            />
        </div>

        @if($availableUsers->count() > 0)
            <div class="grid gap-3">
                @foreach($availableUsers as $user)
                    <div class="flex items-center justify-between p-4 bg-white border rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
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
                                variant="primary" 
                                icon="user-plus"
                                wire:click="grantAccess({{ $user->id }})"
                            >
                                권한 부여
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 페이지네이션 -->
            <div class="mt-4">
                {{ $availableUsers->links('custom-flux-pagination') }}
            </div>
        @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <flux:icon.users class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                @if($searchTerm)
                    <flux:subheading>검색 결과가 없습니다</flux:subheading>
                    <p class="text-sm text-gray-500 mt-1">다른 검색어를 시도해보세요.</p>
                @else
                    <flux:subheading>권한을 부여할 수 있는 사용자가 없습니다</flux:subheading>
                    <p class="text-sm text-gray-500 mt-1">모든 사용자가 이미 권한을 가지고 있습니다.</p>
                @endif
            </div>
        @endif
    </div>
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
