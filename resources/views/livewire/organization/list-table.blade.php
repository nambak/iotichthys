<div x-data="organizationListTable()">
    <div class="shadow-md rounded-lg overflow-hidden w-full overflow-x-auto">
        <table class="w-full mind-w-[720px]">
            <thead>
            <tr>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                    {{ __('조직 이름') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                    {{ __('대표자 명') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                    {{ __('사업자 번호') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                    {{ __('주소') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                    {{ __('사업장 연락처') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                    {{ __('등록일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700">
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
            @forelse ($organizations as $organization)
            <tr class="hover:bg-white/5 transition-colors">
                <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-200">
                    <a href="{{ route('organization.show', $organization) }}"
                       class="text-blue-400 hover:text-blue-300 font-medium transition-colors">
                        {{ $organization->name }}
                    </a>
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    {{ $organization->owner }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    <code class="bg-zinc-800 px-2 py-1 rounded text-xs">{{ $organization->business_register_number }}</code>
                </td>
                <td class="px-3 py-4 text-sm text-zinc-200">
                    <div class="max-w-lg truncate">
                        {{ $organization->address }}
                    </div>
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-200">
                    {{ $organization->phone_number }}
                </td>
                <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-200">
                    {{ $organization->created_at->format('Y-m-d') }}
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <flux:icon.pencil-square
                            class="inline-block size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                            wire:click="editOrganization({{ $organization->id }})"
                    />
                    <flux:icon.trash
                            class="inline-block size-4 hover:text-red-600 transition-colors cursor-pointer"
                            @click="deleteOrganization({{ $organization->id }})"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('조직이 없습니다. 새 조직을 생성해보세요!') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-300">
        {{ $organizations->links('custom-flux-pagination') }}
    </div>
</div>

<script>
    function organizationListTable() {
        return {
            deleteOrganization(organizationId) {
                confirmDelete('정말로 이 조직을 삭제하시겠습니까?', () => {
                    this.$wire.delete(organizationId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event[0].message);
                });

                this.$wire.on('organization-deleted', () => {
                    showSuccessToast('조직이 삭제되었습니다.');
                });

                this.$wire.on('organization-created', () => {
                    showSuccessToast('조직이 생성되었습니다.')
                });

                this.$wire.on('organization-updated', () => {
                    showSuccessToast('조직이 수정되었습니다.');
                });
            }
        }
    }
</script>