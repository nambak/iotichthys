<section class="w-full">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('조직 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('조직을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <!-- TODO: 조직 생성 권한 체크 -->
            <flux:modal.trigger name="create-organization">
                <flux:button  dusk="create-organization-button" variant="primary" icon="plus">
                    {{ __('새 조직 추가') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle"/>
    </div>
    <!-- 조직 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20">
            <thead>
            <tr>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    조직 이름
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    대표자 명
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    사업자 번호
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    주소
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    사업장 연락처
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    등록일
                </th>
                <th scope="col"
                    class="py-3 px-3 text-start text-sm font-medium text-zinc-800 dark:text-white">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20">
            @forelse ($organizations as $organization)
            <tr>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $organization->name }}
                </td>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $organization->owner }}
                </td>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $organization->business_register_number }}
                </td>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $organization->address }}
                </td>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $organization->phone_number }}
                </td>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap">
                    {{ $organization->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="py-3 px-3 text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap flex">
                    <button 
                        wire:click="editOrganization({{ $organization->id }})"
                        class="mr-2 text-blue-600 hover:text-blue-800 transition-colors"
                        title="수정"
                    >
                        <flux:icon.pencil-square class="size-4"/>
                    </button>
                    <button 
                        wire:click="deleteOrganization({{ $organization->id }})"
                        wire:confirm="정말로 이 조직을 삭제하시겠습니까?"
                        class="text-red-600 hover:text-red-800 transition-colors"
                        title="삭제"
                    >
                        <flux:icon.trash class="size-4"/>
                    </button>
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
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $organizations->links('custom-flux-pagination') }}
    </div>

    <!-- 조직 생성 모달 -->
    <livewire:organization.create-modal/>
    
    <!-- 조직 수정 모달 -->
    <livewire:organization.edit-modal/>
</section>

