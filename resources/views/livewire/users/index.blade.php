<section class="w-full" x-data="usersIndex()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('사용자 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('등록된 사용자를 조회하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- 회원가입 기능으로 대체되므로 사용자 등록 기능은 없음 -->
        </div>
        <flux:separator variant="subtle"/>
    </div>
    <!-- 사용자 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
            <thead>
            <tr>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    이름
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    이메일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    상태
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    등록일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    탈퇴일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @forelse ($users as $user)
            <tr>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->name }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->email }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                        {{ $user->isActive() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $user->getStatusText() }}
                    </span>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->withdrawn_at ? $user->withdrawn_at->format('Y-m-d H:i:s') : '-' }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap flex">
                    @if ($user->canBeEdited() && $user->id !== auth()->id())
                        <flux:icon.pencil-square
                                class="size-4 mr-1 hover:text-blue-600 transition-colors"
                                wire:click="edit({{ $user->id }})"
                        />
                    @endif
                    @if ($user->isActive() && $user->id !== auth()->id())
                        <flux:icon.user-minus
                                class="size-4 hover:text-red-600 transition-colors"
                                @click="withdrawUser({{ $user->id }})"
                        />
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('등록된 사용자가 없습니다.') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $users->links('custom-flux-pagination') }}
    </div>

    <!-- 사용자 수정 모달 -->
    <livewire:users.edit-modal/>
</section>

<script>
    function usersIndex() {
        return {
            withdrawUser(userId) {
                confirmDelete('정말로 이 사용자를 탈퇴시키겠습니까?', () => {
                    this.$wire.withdraw(userId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event.message);
                });

                this.$wire.on('user-withdrawn', () => {
                    showSuccessToast('사용자가 탈퇴 처리되었습니다.');
                });

                this.$wire.on('user-updated', () => {
                    showSuccessToast('사용자 정보가 수정되었습니다.');
                });
            }
        }
    }
</script>