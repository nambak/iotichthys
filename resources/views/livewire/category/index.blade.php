<section class="w-full" x-data="categoryIndex()">

    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('카테고리 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('카테고리를 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <flux:modal.trigger name="create-category">
                <flux:button dusk="create-category-button" variant="primary" icon="plus">
                    {{ __('새 카테고리 추가') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 카테고리 목록 테이블 -->
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
                    생성일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                        &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @forelse ($categories as $category)
            <tr>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <a href="{{ route('category.show', $category) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                        {{ $category->name }}
                    </a>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300">
                    <div class="max-w-xs truncate">
                        {{ $category->description ?? '-' }}
                    </div>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    <flux:badge variant="{{ $category->is_active ? 'success' : 'warning' }}">
                        {{ $category->is_active ? '활성' : '비활성' }}
                    </flux:badge>
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $category->created_at->format('Y-m-d') }}
                </td>
                <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap flex">
                    <flux:icon.pencil-square
                            class="size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                            wire:click="editCategory({{ $category->id }})"
                    />
                    <flux:icon.trash
                            class="size-4 hover:text-red-600 transition-colors cursor-pointer"
                            @click="deleteCategory({{ $category->id }})"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('카테고리가 없습니다. 새 카테고리를 생성해보세요!') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $categories->links('custom-flux-pagination') }}
    </div>

    <!-- 카테고리 생성 모달 -->
    <livewire:category.create-modal/>
</section>

<script>
    function categoryIndex() {
        return {
            deleteCategory(categoryId) {
                confirmDelete('정말로 이 카테고리를 삭제하시겠습니까?', () => {
                    this.$wire.delete(categoryId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event[0].message);
                });

                this.$wire.on('category-deleted', () => {
                    showSuccessToast('카테고리가 삭제되었습니다.');
                });

                this.$wire.on('category-created', () => {
                    showSuccessToast('카테고리가 생성되었습니다.')
                });

                this.$wire.on('category-updated', () => {
                    showSuccessToast('카테고리가 수정되었습니다.');
                });
            }
        }
    }
</script>