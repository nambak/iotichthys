<div class="bg-zinc-800 rounded-lg shadow-md p-6" x-data="teamsListTable()">
    @if($showHeader)
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg" level="2">팀 목록</flux:heading>
        <flux:button variant="primary" icon="plus">
            {{ __('팀 추가') }}
        </flux:button>
    </div>
    @endif

    <div class="shadow-md rounded-lg w-full overflow-x-auto bg-zinc-900">
        <table class="w-full min-w-[720px] divide-white/20">
            <thead>
            <tr>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('팀 이름') }}
                </th>
                @if($showOrganizationColumn)
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('조직') }}
                </th>
                @endif
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('설명') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('멤버 수') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('생성일') }}
                </th>
                @if($showActions)
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                </th>
                @endif
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
            @forelse ($teams as $team)
            <tr class="hover:bg-white/5 transition-colors">
                <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-200">
                    @if(Route::has('teams.show'))
                        <a href="{{ route('teams.show', $team) }}" class="text-blue-400 hover:text-blue-300 font-medium transition-colors">
                            {{ $team->name }}
                        </a>
                    @else
                        <span class="text-blue-400 font-medium">
                            {{ $team->name }}
                        </span>
                    @endif
                </td>
                @if($showOrganizationColumn)
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    {{ $team->organization->name }}
                </td>
                @endif
                <td class="px-3 py-4 text-sm text-zinc-200">
                    <div class="max-w-lg truncate">
                        {{ $team->description ?? '-' }}
                    </div>
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    <span class="bg-zinc-800 px-2 py-1 rounded text-xs">{{ $team->users_count }}명</span>
                </td>
                <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-200">
                    {{ $team->created_at->format('Y-m-d') }}
                </td>
                @if($showActions)
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
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $showOrganizationColumn ? ($showActions ? '6' : '5') : ($showActions ? '5' : '4') }}" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('팀이 없습니다. 새 팀을 생성해보세요!') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-300">
        {{ $teams->links('custom-flux-pagination') }}
    </div>
</div>

<script>
    function teamsListTable() {
        return {
            deleteTeam(teamId) {
                confirmDelete('정말로 이 팀을 삭제하시겠습니까?', () => {
                    this.$wire.delete(teamId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event[0].message);
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