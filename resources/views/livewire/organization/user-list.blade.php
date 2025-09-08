<div class="bg-zinc-800 rounded-lg shadow-md p-6" x-data="initUserList()">
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">조직 구성원 ({{ $users->total() }}명)</flux:heading>
    </div>

    @if($users->total() > 0)
    <div class="shadow-md rounded-lg w-full overflow-x-auto">
        <table class="w-full min-w-[720px]">
            <thead>
            <tr>
                <th scope="col" class="py-3 px-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    이름
                </th>
                <th scope="col" class="py-3 px-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    이메일
                </th>
                <th scope="col" class="py-3 px-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    역할
                </th>
                <th scope="col" class="py-3 px-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    가입일
                </th>
                <th scope="col" class="py-3 px-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    <span class="sr-only">작업</span>
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
            @foreach ($users as $user)
            <tr class="hover:bg-white/5 transition-colors">
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    {{ $user->name }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    {{ $user->email }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    @if($user->pivot->is_owner)
                    <flux:badge color="amber">소유자</flux:badge>
                    @else
                    <flux:badge color="zinc">구성원</flux:badge>
                    @endif
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    {{ $user->pivot->created_at->format('Y-m-d') }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm  text-zinc-200">
                    @if(!$user->pivot->is_owner)
                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:text-red-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40">
                        <flux:icon.trash
                                class="w-4 h-4"
                                @click="removeUser({{ $user->id }})"
                        />
                    </button>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-300">
        {{ $users->links('custom-flux-pagination') }}
    </div>
    @else
    <div class="text-center py-8">
        <flux:icon.users class="mx-auto h-12 w-12 text-gray-400"/>
        <flux:heading size="md" class="mt-2 text-gray-400">조직에 속한 구성원이 없습니다</flux:heading>
        <flux:subheading class="mt-1 text-gray-500">새로운 구성원을 추가해보세요.</flux:subheading>
    </div>
    @endif
</div>

<script>
    function initUserList() {
        return {
            isRemoving: false,
            removeUser(userId) {
                const exec = () => {
                    if (this.isRemoving) return;
                    this.isRemoving = true;
                    this.$wire.removeUserFromOrganization(userId)
                        .finally(() => { this.isRemoving = false; });
                };
                if (typeof window.confirmDelete === 'function') {
                    window.confirmDelete(
                        '정말로 이 사용자를 조직에서 제거하시겠습니까?',
                        exec
                    )
                }
            },
        }
    }
</script>