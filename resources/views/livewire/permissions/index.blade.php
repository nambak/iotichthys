<section class="w-full" x-data="permissionsIndex()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('권한 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('시스템 권한을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- TODO: 권한 생성 권한 체크 -->
            <flux:modal.trigger name="create-permission">
                <flux:button dusk="create-permission-button" variant="primary" icon="plus">
                    {{ __('새 권한 생성') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle"/>
    </div>
    <!-- 권한 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
            <thead>
            <tr>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    권한 이름
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    슬러그
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    리소스
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    액션
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    설명
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    등록일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @forelse ($permissions as $permission)
            <tr>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $permission->name }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $permission->slug }}</code>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                        {{ $permission->resource }}
                    </span>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                        {{ $permission->action }}
                    </span>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300">
                    {{ $permission->description ?? '-' }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $permission->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap flex">
                    <flux:icon.pencil-square
                            class="size-4 mr-1 hover:text-blue-600 transition-colors"
                            wire:click="editPermission({{ $permission->id }})"
                    />
                    <flux:icon.trash
                            class="size-4 hover:text-red-600 transition-colors"
                            @click="deletePermission({{ $permission->id }})"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('권한이 없습니다. 새 권한을 생성해보세요!') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $permissions->links('custom-flux-pagination') }}
    </div>


    <!-- 권한 생성 모달 -->
    <livewire:permissions.create-modal/>

    <!-- 권한 수정 모달 -->
    <livewire:permissions.edit-modal/>
</section>

<script>
    function permissionsIndex() {
        return {
            deletePermission(permissionId) {
                confirmDelete('정말로 이 권한을 삭제하시겠습니까?', () => {
                    this.$wire.delete(permissionId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event.message);
                });

                this.$wire.on('permission-deleted', () => {
                    showSuccessToast('권한이 삭제되었습니다.');
                });

                this.$wire.on('permission-created', () => {
                    showSuccessToast('권한이 생성되었습니다.')
                });

                this.$wire.on('permission-updated', () => {
                    showSuccessToast('권한이 수정되었습니다.');
                });
            }
        }
    }
</script>