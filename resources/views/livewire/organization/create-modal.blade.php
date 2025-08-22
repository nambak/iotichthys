<div x-data="createModal()">
    <flux:modal name="create-organization" class="w-1/2" @close="resetForm">
        <div>
            <flux:heading size="lg">{{ __('새 조직 추가') }}</flux:heading>
        </div>
        <form wire:submit.prevent="save" class="font-size-[14px] mt-10">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                            label="사업자명"
                            placeholder="사업자명을 입력해 주세요"
                            wire:model="name"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                            label="대표자 명"
                            placeholder="대표자 명을 입력해 주세요"
                            wire:model="owner"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                            label="사업자 번호"
                            placeholder="-을 제외한 사업자 번호를 입력해 주세요"
                            wire:model="business_register_number"
                    />
                </flux:field>
                <flux:field>
                    <div class="grid grid-cols-[5fr_1fr] items-top gap-2">
                        <flux:input
                                label="우편번호"
                                id="postcode-input"
                                class="w-full"
                                wire:model="postcode"
                        />
                        <flux:button
                                class="w-fit mt-6"
                                dusk="search-address-button"
                                @click="openAddressSearch"
                        >주소 검색
                        </flux:button>
                    </div>
                </flux:field>
                <flux:field>
                    <flux:input
                            label="주소"
                            id="address-input"
                            class="mt-2"
                            wire:model="address"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                            label="상세주소"
                            id="detail-address-input"
                            wire:model="detail_address"
                    />
                </flux:field>
                <flux:field>
                    <flux:input
                            label="사업자 전화번호"
                            placeholder="-를 제외한 대표 전화 번호를 입력해 주세요 "
                            wire:model="phone_number"
                    />
                </flux:field>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">{{ __('저장') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    function createModal() {
        return {
            openAddressSearch() {
                new daum.Postcode({
                    oncomplete: function (data) {
                        // 🔥 @this.set()으로 Livewire 프로퍼티에 직접 값 설정
                        @this.set('postcode', data.zonecode || '')
                        @this.set('address', data.roadAddress || '')

                        // 상세주소 입력 필드에 포커스
                        const detailAddressInput = document.getElementById('detail-address-input');
                        if (detailAddressInput) {
                            detailAddressInput.focus();
                        }

                    },
                    onerror: function (error) {
                        showErrorToast('주소 검색 서비스에 일시적인 문제가 발생했습니다. 잠시 후 다시 시도해주세요.');
                        console.error('Daum Postcode API Error:', error);
                    },

                    // 팝업 스타일 설정
                    width: '100%',
                    height: '100%',

                    // 팝업이 닫힐 때
                    onclose: function (state) {
                        if (state === 'FORCE_CLOSE') {
                            console.log('주소 검색이 취소되었습니다.');
                        }
                    }
                }).open();
            }
        }
    }
</script>
