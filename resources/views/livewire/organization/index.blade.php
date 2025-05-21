<section class="w-full">
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('조직 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('조직을 생성하고 관리합니다.') }}</flux:subheading>
            </div>
            <flux:modal.trigger name="create-organization">
                <flux:button variant="primary" wire:click="create" icon="plus">{{ __('새 조직 추가') }}</flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle" />
    </div>
    <!-- 조직 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-y divide-gray-200">
            <thead>
            <tr>
                <th scope="col" class="px-6 py-3 text-center font-medium uppercase tracking-wider">
                    이름
                </th>
                <th scope="col" class="px-6 py-3 text-center font-medium uppercase tracking-wider">
                    설명
                </th>
                <th scope="col" class="px-6 py-3 text-center font-medium uppercase tracking-wider">
                    멤버
                </th>
                <th scope="col" class="px-6 py-3 text-center font-medium uppercase tracking-wider">
                    역할
                </th>
                <th scope="col" class="px-6 py-3 text-center font-medium uppercase tracking-wider">
                    액션
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @forelse ($organizations as $organization)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-lg">{{ mb_substr($organization->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">
                                {{ $organization->name }}
                            </div>
                            <div>
                                {{ $organization->slug }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="line-clamp-2">{{ $organization->description ?: '설명 없음' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div>{{ $organization->users_count ?? $organization->users()->count() }} 명</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if ($organization->pivot && $organization->pivot->is_owner)
                        <span class="px-2 py-1 inline-flex leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            관리자
                        </span>
                    @else
                        <span class="px-2 py-1 inline-flex leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            멤버
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center font-medium">
                    <a href="{{ route('organizations.show', $organization) }}" class="text-primary mr-3">보기</a>

                    @if ($organization->pivot && $organization->pivot->is_owner)
                        <button wire:click="edit({{ $organization->id }})" class="mr-3">수정</button>
                        <button wire:click="$emit('openDeleteModal', {{ $organization->id }})">삭제</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('조직이 없습니다. 새 조직을 생성해보세요!') }}
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4">
        {{ $organizations->links() }}
    </div>

    <!-- 조직 생성 모달 -->
    <livewire:organization.create-modal />
</section>

