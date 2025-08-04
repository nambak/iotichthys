<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">조직 구성원 ({{ $users->total() }}명)</flux:heading>
    </div>

    @if($users->total() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
            <thead>
            <tr>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    이름
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    이메일
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    역할
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    가입일
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @foreach ($users as $user)
            <tr>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->name }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->email }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    @if($user->pivot->is_owner)
                    <flux:badge color="amber">소유자</flux:badge>
                    @else
                    <flux:badge color="zinc">구성원</flux:badge>
                    @endif
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $user->pivot->created_at->format('Y-m-d') }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
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
