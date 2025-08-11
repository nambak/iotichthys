<section class="w-full" x-data="categoryShow()">

    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $category->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">
                    {{ $category->description ?: '카테고리 상세 정보 및 하위 카테고리를 관리합니다.' }}
                </flux:subheading>
                
                <!-- 카테고리 경로 표시 -->
                <div class="mb-4">
                    <flux:badge variant="subtle">
                        <a href="{{ route('category.index') }}" class="hover:underline">카테고리</a>
                        > {{ $category->name }}
                    </flux:badge>
                </div>
            </div>

            <div class="flex space-x-2">
                <flux:button variant="outline" href="{{ route('category.index') }}">
                    목록으로
                </flux:button>
                <flux:modal.trigger name="create-category">
                    <flux:button dusk="create-subcategory-button" variant="primary" icon="plus" wire:click="createSubcategory">
                        {{ __('하위 카테고리 추가') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 카테고리 정보 카드 -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <flux:label>카테고리 이름</flux:label>
                <div class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">{{ $category->name }}</div>
            </div>
            <div>
                <flux:label>상태</flux:label>
                <div class="mt-1">
                    <flux:badge variant="{{ $category->is_active ? 'success' : 'warning' }}">
                        {{ $category->is_active ? '활성' : '비활성' }}
                    </flux:badge>
                </div>
            </div>
            <div>
                <flux:label>정렬 순서</flux:label>
                <div class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">{{ $category->sort_order }}</div>
            </div>
            <div class="md:col-span-3">
                <flux:label>설명</flux:label>
                <div class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">
                    {{ $category->description ?: '설명이 없습니다.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 하위 카테고리 목록 -->
    <div class="mb-6">
        <flux:heading size="lg" level="2" class="mb-4">하위 카테고리 ({{ $subcategories->total() }}개)</flux:heading>
        
        @if($subcategories->count() > 0)
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
                        하위 카테고리 수
                    </th>
                    <th scope="col"
                        class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                        정렬 순서
                    </th>
                    <th scope="col"
                        class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                        상태
                    </th>
                    <th scope="col"
                        class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                        &nbsp;
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
                @foreach ($subcategories as $subcategory)
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
                        <flux:badge variant="{{ $subcategory->children_count > 0 ? 'outline' : 'subtle' }}">
                            {{ $subcategory->children_count }}개
                        </flux:badge>
                    </td>
                    <td class="py-3 px-3 text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                        {{ $subcategory->sort_order }}
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
                                @click="deleteSubcategory({{ $subcategory->id }})"
                        />
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- 페이지네이션 -->
        <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
            {{ $subcategories->links('custom-flux-pagination') }}
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <flux:icon.folder-open class="size-12 mx-auto mb-4 text-gray-300" />
            <p>하위 카테고리가 없습니다.</p>
            <p class="text-sm">하위 카테고리를 추가해보세요!</p>
        </div>
        @endif
    </div>

    <!-- 카테고리 생성 모달 -->
    <livewire:category.create-modal/>
</section>

<script>
    function categoryShow() {
        return {
            deleteSubcategory(categoryId) {
                confirmDelete('정말로 이 하위 카테고리를 삭제하시겠습니까?', () => {
                    this.$wire.deleteSubcategory(categoryId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event[0].message);
                });

                this.$wire.on('subcategory-deleted', () => {
                    showSuccessToast('하위 카테고리가 삭제되었습니다.');
                });

                this.$wire.on('subcategory-created', () => {
                    showSuccessToast('하위 카테고리가 생성되었습니다.')
                });

                this.$wire.on('subcategory-updated', () => {
                    showSuccessToast('하위 카테고리가 수정되었습니다.');
                });
            }
        }
    }
</script>