<flux:modal name="create-organization" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('새 조직 추가') }}</flux:heading>
        </div>
        <flux:input label="조직 이름" placeholder="조직 이름을 입력해 주세요" />
        <flux:input label="대표자 명" placeholder="대표자 명을 입력해 주세요" />
        <flux:input label="사업자 번호" placeholder="-을 제외한 사업자 번호를 입력해 주세요" />
        <flux:input label="사업장 주소" placeholder="사업장 주소를 입력해 주세요" />
        <flux:input label="사업자 전화번호" placeholder="-를 제외한 대표 전화 번호를 입력해 주세요 " />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save changes</flux:button>
        </div>
    </div>
</flux:modal>
