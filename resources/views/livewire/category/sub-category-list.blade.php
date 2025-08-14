<div class="mb-6" x-data="subCategoryList()">
    <div class="flex justify-between items-center">
        <flux:heading size="lg" level="2" class="mb-4">하위 카테고리 ({{ $subCategories->total() }}개)</flux:heading>
        <div class="flex space-x-2">
            <flux:modal.trigger name="create-category">
                <flux:button dusk="create-subcategory-button" variant="primary" icon="plus" wire:click="createSubcategory">
                    {{ __('하위 카테고리 추가') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    @if($subCategories->count() > 0)
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
            <thead>
            <tr>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    카테고리 이름
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    설명
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    상태
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @foreach ($subCategories as $subcategory)
            <tr>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <a href="{{ route('category.show', $subcategory) }}"
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                        {{ $subcategory->name }}
                    </a>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300">
                    <div class="max-w-xs truncate">
                        {{ $subcategory->description ?? '-' }}
                    </div>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <flux:badge variant="{{ $subcategory->is_active ? 'success' : 'warning' }}">
                        {{ $subcategory->is_active ? '활성' : '비활성' }}
                    </flux:badge>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap flex">
                    <flux:icon.pencil-square
                            class="size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                            wire:click="editSubcategory({{ $subcategory->id }})"
                    />
                    <flux:icon.trash
                            class="size-4 hover:text-red-600 transition-colors cursor-pointer"
                            x-on:click="deleteSubcategory({{ $subcategory->id }})"
                    />
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $subCategories->links('custom-flux-pagination') }}
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <flux:icon.folder-open class="size-12 mx-auto mb-4 text-gray-300" />
        <p>하위 카테고리가 없습니다.</p>
        <p class="text-sm">하위 카테고리를 추가해보세요!</p>
    </div>
    @endif
</div>

<script>
    function subCategoryList() {
        return {
            deleteSubcategory(categoryId) {
                confirmDelete('정말로 이 하위 카테고리를 삭제하시겠습니까?', () => {
                    this.$wire.deleteSubcategory(categoryId);
                });
            },

            init() {
                this.$wire.on('subcategory-deleted', (event) => {
                    showSuccessToast('하위 카테고리가 삭제되었습니다.');
                });
            }
        }
    }
</script>