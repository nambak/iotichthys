<section class="w-full" x-data="deviceIndex()">

    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1" class="mb-6">{{ __('장치 관리') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">{{ __('MQTT IoT 장치를 생성하고 관리합니다.') }}</flux:subheading>
            </div>

            <flux:modal.trigger name="create-device">
                <flux:button dusk="create-device-button" variant="primary" icon="plus">
                    {{ __('새 장치 추가') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        <flux:separator variant="subtle"/>
    </div>

    <!-- 장치 목록 테이블 -->
    <div class="shadow-md rounded-lg overflow-hidden w-full">
        <table class="w-full divide-y divide-white/20">
            <thead>
            <tr>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    장치명
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    장치 ID
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    모델
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    상태
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    소속 조직
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    설정 수
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                    등록일
                </th>
                <th scope="col" class="py-3 px-3 text-start text-sm font-medium text-white bg-zinc-700/80">
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-700 divide-white/20">
            @forelse($devices as $device)
                <tr>
                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                        {{ $device->name }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                        <code class="bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded text-xs">{{ $device->device_id }}</code>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                        @if($device->deviceModel)
                            <a href="{{ route('device-model.show', $device->deviceModel) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $device->deviceModel->name }}
                            </a>
                        @else
                            알 수 없음
                        @endif
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                        <flux:badge variant="{{ $device->status === 'active' ? 'lime' : ($device->status === 'error' ? 'red' : 'zinc') }}" size="sm">
                            {{ $device->status_text }}
                        </flux:badge>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $device->organization->name ?? '없음' }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $device->configs_count }}개
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $device->created_at->format('Y-m-d') }}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <flux:icon.pencil-square
                                class="size-4 mr-1 hover:text-blue-600 transition-colors cursor-pointer"
                                wire:click="editDevice({{ $device->id }})"
                        />
                        <flux:icon.trash
                                class="size-4 hover:text-red-600 transition-colors cursor-pointer"
                                @click="deleteDevice({{ $device->id }})"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-3 py-8 text-center text-sm text-zinc-400">
                        등록된 장치가 없습니다.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4 text-xs px-1 text-zinc-500 dark:text-zinc-300">
        {{ $devices->links('custom-flux-pagination') }}
    </div>

    <!-- 장치 생성 모달 -->
    <livewire:device.create-modal/>

    <!-- 장치 편집 모달 -->
    <livewire:device.edit-modal/>

</section>

<script>
function deviceIndex() {
    return {
        deleteCategory(deviceId) {
            confirmDelete('정말로 이 장치를 삭제하시겠습니까?', () => {
                this.$wire.delete(deviceId);
            });
        },

        init() {
            this.$wire.on('show-error-toast', (event) => {
                showErrorToast(event[0].message);
            });

            this.$wire.on('device-deleted', () => {
                showSuccessToast('장치가 삭제되었습니다.');
            });

            this.$wire.on('device-created', () => {
                showSuccessToast('장치가 생성되었습니다.')
            });

            this.$wire.on('device-updated', () => {
                showSuccessToast('장치가 수정되었습니다.');
            });
        }
    }
}
</script>
