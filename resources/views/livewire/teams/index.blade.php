<section class="w-full" x-data="teamsIndex()">
    <div class="relative mb-3 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('팀 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-3">{{ __('팀을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- TODO: 팀 생성 권한 체크 -->
            <flux:modal.trigger name="create-team">
                <flux:button dusk="create-team-button" variant="primary" icon="plus">
                    {{ __('새 팀 생성') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>
    <!-- 팀 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-white/20">
            <thead>
            <tr>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('팀 이름') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('조직') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('설명') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('구성원 수') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('등록일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
                @forelse ($teams as $team)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $team->name }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $team->organization->name }}
                        </td>
                        <td class="px-3 py-4 text-sm text-zinc-800 dark:text-zinc-200">
                            <div class="max-w-lg truncate">
                                {{ $team->description ?? '설명 없음' }}
                            </div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            <flux:badge variant="{{ $team->users_count > 0 ? 'blue' : 'zinc' }}" size="sm">
                                {{ $team->users_count }}명
                            </flux:badge>
                        </td>
                        <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $team->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <flux:icon.pencil-square
                                    class="inline-block size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                                    wire:click="editTeam({{ $team->id }})"
                            />
                            <flux:icon.trash
                                    class="inline-block size-4 hover:text-red-600 transition-colors cursor-pointer"
                                    @click="deleteTeam({{ $team->id }})"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('팀이 없습니다. 새 팀을 생성해보세요!') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $teams->links('custom-flux-pagination') }}
    </div>


    <!-- 팀 생성 모달 -->
    <livewire:teams.create-modal/>

    <!-- 팀 수정 모달 -->
    <livewire:teams.edit-modal/>
</section>

<script>
    function teamsIndex() {
        return {
            deleteTeam(teamId) {
                confirmDelete('정말로 이 팀을 삭제하시겠습니까?', () => {
                    this.$wire.delete(teamId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event.message);
                });

                this.$wire.on('team-deleted', () => {
                    showSuccessToast('팀이 삭제되었습니다.');
                });

                this.$wire.on('team-created', () => {
                    showSuccessToast('팀이 생성되었습니다.')
                });

                this.$wire.on('team-updated', () => {
                    showSuccessToast('팀이 수정되었습니다.');
                });
            }
        }
    }
</script>