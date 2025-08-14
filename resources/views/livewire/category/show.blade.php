<section class="w-full" x-data="categoryShow()">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $category->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">
                    {{ $category->description ?: '카테고리 상세 정보 및 하위 카테고리를 관리합니다.' }}
                </flux:subheading>
                
                <!-- 카테고리 경로 표시 -->
                <livewire:category.breadcrumb :category="$category" />
            </div>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 탭 인터페이스 -->
    <div x-data="{ activeTab: 'overview' }" class="w-full" 
         x-on:tab-changed.window="activeTab = $event.detail.tab">
        <!-- 탭 헤더 컴포넌트 -->
        <livewire:category.tab-header :active-tab="'overview'" />

        <!-- 탭 콘텐츠 -->
        <div class="mt-6">
            <!-- 개요 탭 -->
            <div x-show="activeTab === 'overview'" class="space-y-6" x-transition>
                <!-- 카테고리 정보 카드 -->
                <livewire:category.detail-card :category="$category" />

                <!-- 하위 카테고리 목록 -->
                <livewire:category.sub-category-list :category="$category" />
            </div>

            <!-- 권한 탭 -->
            <div x-show="activeTab === 'permissions'" class="space-y-6" x-transition>
                <livewire:category.permissions :category="$category" />
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