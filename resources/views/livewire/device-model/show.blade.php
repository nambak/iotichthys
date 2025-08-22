<section class="w-full">

    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <flux:button variant="ghost" size="sm" icon="arrow-left" wire:click="goBack">
                        뒤로 가기
                    </flux:button>
                </div>
                <flux:heading size="xl" level="1" class="mb-2">{{ $deviceModel->name }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('장치 모델 상세 정보') }}</flux:subheading>
            </div>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 모델 정보 카드 -->
    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 기본 정보 -->
            <div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">기본 정보</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">모델명</dt>
                        <dd class="text-sm text-zinc-900 dark:text-zinc-100">{{ $deviceModel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">제조사</dt>
                        <dd class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $deviceModel->manufacturer ?? '제조사 미상' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">설명</dt>
                        <dd class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $deviceModel->description ?? '설명이 없습니다.' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">생성일</dt>
                        <dd class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $deviceModel->created_at->format('Y년 m월 d일 H:i') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">수정일</dt>
                        <dd class="text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $deviceModel->updated_at->format('Y년 m월 d일 H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- 사양 정보 -->
            <div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">사양</h3>
                @if($deviceModel->specifications && count($deviceModel->specifications) > 0)
                    <ul class="space-y-2">
                        @foreach($deviceModel->specifications as $spec)
                            <li class="text-sm text-zinc-900 dark:text-zinc-100 flex items-start">
                                <flux:icon.check-circle class="size-4 text-green-600 mt-0.5 mr-2 flex-shrink-0"/>
                                {{ $spec }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">사양 정보가 없습니다.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- 사용 중인 장치 목록 -->
    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                이 모델을 사용하는 장치 
                <flux:badge variant="blue" size="sm" class="ml-2">{{ $devices->total() }}개</flux:badge>
            </h3>
        </div>

        @if($devices->count() > 0)
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                        {{ __('장치명') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                        {{ __('장치 ID') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                        {{ __('상태') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                        {{ __('소속 조직') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                        {{ __('생성일') }}
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($devices as $device)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $device->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            {{ $device->device_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            <flux:badge variant="{{ $device->status === 'active' ? 'lime' : ($device->status === 'error' ? 'red' : 'zinc') }}" size="sm">
                                {{ $device->status_text }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            {{ $device->organization->name ?? '없음' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            {{ $device->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- 페이지네이션 -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $devices->links('custom-flux-pagination') }}
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <div class="text-zinc-500 dark:text-zinc-400">
                    <flux:icon.device-phone-mobile class="size-12 mx-auto mb-4"/>
                    <p class="text-lg font-medium mb-2">사용 중인 장치가 없습니다</p>
                    <p class="text-sm">이 모델을 사용하는 장치가 등록되지 않았습니다.</p>
                </div>
            </div>
        @endif
    </div>

</section>