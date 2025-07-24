<div x-data="createModal()">
    <flux:modal name="create-organization" class="md:w-96" @close="resetForm">
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
                    <flux:label>사업장 주소</flux:label>
                    <div class="grid grid-cols-[3fr_1fr] items-end gap-2">
                        <flux:input
                                class="w-full"
                                placeholder="우편번호"
                                wire:model="postcode"
                        />
                        <flux:button
                                class="w-fit"
                                dusk="search-address-button"
                                @click="openAddressSearch"
                        >주소 검색
                        </flux:button>
                    </div>
                    <flux:input placeholder="주소" wire:model="address"/>
                    <flux:input placeholder="상세주소" wire:model="detail_address"/>
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
                        // 각 주소의 타입을 확인
                        let addr = '';
                        let extraAddr = '';

                        addr = data.roadAddress;

                        // 법정동명이 있을 때 추가한다. (법정리는 제외)
                        if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                            extraAddr += data.bname;
                        }
                        // 건물명이 있고, 공동주택일 때 추가한다.
                        if (data.buildingName !== '' && data.apartment === 'Y') {
                            extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                        }
                        // 표시할 참고항목이 있을 때, 괄호까지 추가한 최종 문자열을 만든다.
                        if (extraAddr !== '') {
                            extraAddr = ' (' + extraAddr + ')';
                        }

                        console.log('addressData :', data);

                        // Livewire 컴포넌트에 주소 데이터 전달
                        Livewire.dispatch('address-selected', {
                            postcode: data.zonecode,
                            address: data.roadAddress,
                        });
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
