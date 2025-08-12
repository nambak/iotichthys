<section class="w-full" x-data="categoryShow()">

    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $category->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">
                    {{ $category->description ?: '카테고리 상세 정보 및 하위 카테고리를 관리합니다.' }}
                </flux:subheading>
                
                <!-- 카테고리 경로 표시 -->
                <livewire:category.category-breadcrumb :category="$category" />
            </div>

            <div class="flex space-x-2">
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
    <livewire:category.detail-card :category="$category" />

    <!-- 하위 카테고리 목록 -->
    <livewire:category.subcategory-list :category="$category" />

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