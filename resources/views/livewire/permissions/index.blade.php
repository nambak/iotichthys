<section class="w-full" x-data="permissionsIndex()">
    <div class="relative mb-3 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('권한 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-3">{{ __('시스템 권한을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- TODO: 권한 생성 권한 체크 -->
            <flux:modal.trigger name="create-permission">
                <flux:button dusk="create-permission-button" variant="primary" icon="plus">
                    {{ __('새 권한 생성') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>
    <!-- 권한 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-white/20">
            <thead>
            <tr>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('권한 이름') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('리소스') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('액션') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('설명') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('사용자 수') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('등록일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
                @forelse ($permissions as $permission)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            <a href="{{ route('permissions.show', $permission) }}" 
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $permission->name }}
                            </a>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            <flux:badge variant="blue" size="sm">
                                {{ $permission->resource }}
                            </flux:badge>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            <flux:badge variant="lime" size="sm">
                                {{ $permission->action }}
                            </flux:badge>
                        </td>
                        <td class="px-3 py-4 text-sm text-zinc-800 dark:text-zinc-200">
                            <div class="max-w-lg truncate">
                                {{ $permission->description ?? '설명 없음' }}
                            </div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            <flux:badge variant="{{ $permission->roles->flatMap->users->unique('id')->count() > 0 ? 'blue' : 'zinc' }}" size="sm">
                                {{ $permission->roles->flatMap->users->unique('id')->count() }}명
                            </flux:badge>
                        </td>
                        <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $permission->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <flux:icon.pencil-square
                                    class="inline-block size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                                    wire:click="edit({{ $permission->id }})"
                            />
                            <flux:icon.trash
                                    class="inline-block size-4 hover:text-red-600 transition-colors cursor-pointer"
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
                    showErrorToast(event[0].message);
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