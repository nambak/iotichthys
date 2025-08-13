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

    <!-- 탭 인터페이스 -->
    <div x-data="{ activeTab: 'overview' }" class="w-full">
        <!-- 탭 헤더 -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button 
                    @click="activeTab = 'overview'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview' }"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    <flux:icon.information-circle class="w-4 h-4 mr-2 inline" />
                    개요
                </button>
                <button 
                    @click="activeTab = 'permissions'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'permissions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'permissions' }"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    <flux:icon.users class="w-4 h-4 mr-2 inline" />
                    권한
                </button>
            </nav>
        </div>

        <!-- 탭 콘텐츠 -->
        <div class="mt-6">
            <!-- 개요 탭 -->
            <div x-show="activeTab === 'overview'" class="space-y-6">
                <!-- 카테고리 정보 카드 -->
                <livewire:category.detail-card :category="$category" />

                <!-- 하위 카테고리 목록 -->
                <livewire:category.sub-category-list :category="$category" />
            </div>

            <!-- 권한 탭 -->
            <div x-show="activeTab === 'permissions'" class="space-y-6">
                <livewire:category.category-permissions :category="$category" />
            </div>
        </div>
    </div>

    <!-- 카테고리 생성 모달 -->
    <livewire:category.create-modal/>
    
    <!-- 카테고리 수정 모달 -->
    <livewire:category.edit-modal/>
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