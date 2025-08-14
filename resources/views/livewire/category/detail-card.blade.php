<div class="bg-zinc-800 rounded-lg shadow-sm border border-zinc-700 p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <flux:label>카테고리 이름</flux:label>
            <div class="text-sm text-zinc-300 mt-1">{{ $category->name }}</div>
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
            <div>
                <flux:label>설명</flux:label>
                <div class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">
                    {{ $category->description ?: '설명이 없습니다.' }}
                </div>
            </div>
        </div>
    </div>
</div>
