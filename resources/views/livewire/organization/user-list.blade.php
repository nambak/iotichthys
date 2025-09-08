<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">조직 구성원 ({{ $users->total() }}명)</flux:heading>
    </div>

    @if($users->total() > 0)
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-white/20">
            <thead>
            <tr>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    이름
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    이메일
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    역할
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    가입일
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
            @foreach ($users as $user)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-200">
                        {{ $user->name }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm  text-zinc-200">
                        {{ $user->email }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm  text-zinc-200">
                        @if($user->pivot->is_owner)
                        <flux:badge color="amber">소유자</flux:badge>
                        @else
                        <flux:badge color="zinc">구성원</flux:badge>
                        @endif
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm  text-zinc-200">
                        {{ $user->pivot->created_at->format('Y-m-d') }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-center text-sm  text-zinc-200">
                        @if(!$user->pivot->is_owner)
                        <flux:icon.trash
                                class="size-4 hover:text-red-600 transition-colors cursor-pointer"
                                @click="removeUser({{ $user->id }})"
                        />
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $users->links('custom-flux-pagination') }}
    </div>
    @else
    <div class="text-center py-8">
        <flux:icon.users class="mx-auto h-12 w-12 text-gray-400" />
        <flux:heading size="md" class="mt-2 text-gray-600 dark:text-gray-400">조직에 속한 구성원이 없습니다</flux:heading>
        <flux:subheading class="mt-1 text-gray-500">새로운 구성원을 추가해보세요.</flux:subheading>
    </div>
    @endif
</div>
