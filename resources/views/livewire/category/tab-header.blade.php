<!-- 탭 헤더 -->
<div class="border-b border-gray-500 mb-6" x-data="{ activeTab: @entangle('activeTab') }">
    <nav class="-mb-px flex">
        <button
                @click="activeTab = 'overview'; $wire.setActiveTab('overview')"
                :class="{'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview'}"
                class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition-colors duration-200"
        >
            <flux:icon.information-circle class="w-4 h-4 mr-2 inline"/>
            개요
        </button>
        <button
                @click="activeTab = 'permissions'; $wire.setActiveTab('permissions')"
                :class="{'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'permissions'}"
                class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition-colors duration-200"
        >
            <flux:icon.users class="w-4 h-4 mr-2 inline"/>
            권한
        </button>
    </nav>
</div>