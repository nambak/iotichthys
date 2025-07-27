<section class="w-full" x-data="teamsIndex()">

    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('팀 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('팀을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <flux:modal.trigger name="create-team">
                <flux:button dusk="create-team-button" variant="primary" icon="plus">
                    {{ __('새 팀 생성') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle"/>
    </div>
    
    <!-- 팀 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
            <thead>
            <tr>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    팀명
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    팀 식별자
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    소속 조직
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    설명
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    멤버 수
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    생성일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @forelse ($teams as $team)
            <tr>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $team->name }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $team->slug }}</code>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $team->organization->name }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300">
                    {{ Str::limit($team->description, 50) }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $team->users_count }}명
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $team->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap flex">
                    <flux:icon.trash
                            class="size-4 hover:text-red-600 transition-colors"
                            @click="deleteTeam({{ $team->id }})"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
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
            }
        }
    }
</script>