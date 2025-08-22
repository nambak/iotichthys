<section class="w-full" x-data="deviceModelIndex()">
    <div class="relative mb-3 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('장치 모델 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-3">{{ __('장치 모델을 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <flux:modal.trigger name="create-device-model">
                <flux:button dusk="create-device-model-button" variant="primary" icon="plus">
                    {{ __('새 모델 추가') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- 장치 모델 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-white/20">
            <thead>
            <tr>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('모델명') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('제조사') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('설명') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('사용 장치 수') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                    {{ __('생성일') }}
                </th>
                <th class="px-3 py-3 text-center text-sm font-medium text-white bg-zinc-700/80">
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700/50 divide-white/10">
                @forelse($deviceModels as $deviceModel)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            <a href="{{ route('device-model.show', $deviceModel) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $deviceModel->name }}
                            </a>
                        </td>
                        <td class="px-3 py-4 text-center text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $deviceModel->manufacturer ?? '제조사 미상' }}
                        </td>
                        <td class="px-3 py-4 text-sm text-zinc-800 dark:text-zinc-200">
                            <div class="max-w-lg truncate">
                                {{ $deviceModel->description ?? '설명 없음' }}
                            </div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-zinc-800 dark:text-zinc-200">
                            <flux:badge variant="{{ $deviceModel->devices_count > 0 ? 'blue' : 'zinc' }}" size="sm">
                                {{ $deviceModel->devices_count }}개
                            </flux:badge>
                        </td>
                        <td class="px-3 py-4 text-center whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $deviceModel->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <flux:icon.pencil-square
                                    class="inline-block size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                                    wire:click="editDeviceModel({{ $deviceModel->id }})"
                            />
                            <flux:icon.trash
                                    class="inline-block size-4 hover:text-red-600 transition-colors cursor-pointer"
                                    @click="deleteDeviceModel({{ $deviceModel->id }})"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('등록된 장치 모델이 없습니다. 새 모델을 생성해보세요!') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $deviceModels->links('custom-flux-pagination') }}
    </div>

    <!-- 장치 모델 생성 모달 -->
    <livewire:device-model.create-modal/>

    <!-- 장치 모델 편집 모달 -->
    <livewire:device-model.edit-modal/>

</section>

<script>
    function deviceModelIndex() {
        return {
            deleteDeviceModel(deviceModelId) {
                confirmDelete('정말로 이 장치 모델을 삭제하시겠습니까?', () => {
                    this.$wire.delete(deviceModelId);
                });
            },

            init() {
                this.$wire.on('show-error-toast', (event) => {
                    showErrorToast(event[0].message);
                });

                this.$wire.on('device-model-deleted', () => {
                    showSuccessToast('장치 모델이 삭제되었습니다.');
                });

                this.$wire.on('device-model-created', () => {
                    showSuccessToast('장치 모델이 생성되었습니다.');
                });

                this.$wire.on('device-model-updated', () => {
                    showSuccessToast('장치 모델이 수정되었습니다.');
                });
            }
        };
    }
</script>