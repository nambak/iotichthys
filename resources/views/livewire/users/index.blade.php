<section class="w-full" x-data="usersIndex()">
    <div class="relative mb-3 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('사용자 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-3">{{ __('등록된 사용자를 조회하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- 회원가입 기능으로 대체되므로 사용자 등록 기능은 없음 -->
        </div>
    </div>
    <!-- 사용자 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-white/20">
            <thead>
            <tr>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('이름') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('이메일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('상태') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('등록일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('탈퇴일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
                @forelse ($users as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $user->name }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $user->email }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            <flux:badge variant="{{ $user->isActive() ? 'lime' : 'red' }}" size="sm">
                                {{ $user->getStatusText() }}
                            </flux:badge>
                        </td>
                        <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $user->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $user->deleted_at ? $user->deleted_at->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            @if ($user->canBeEdited() && $user->id !== auth()->id())
                                <flux:icon.pencil-square
                                        class="inline-block size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                                        wire:click="edit({{ $user->id }})"
                                />
                            @endif
                            @if ($user->isActive() && $user->id !== auth()->id())
                                <flux:icon.user-minus
                                        class="inline-block size-4 hover:text-red-600 transition-colors cursor-pointer"
                                        @click="withdrawUser({{ $user->id }})"
                                />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('등록된 사용자가 없습니다. 새 사용자를 등록해보세요!') }}
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