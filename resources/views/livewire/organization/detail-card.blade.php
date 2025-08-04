<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 mb-6">
    <flux:heading size="lg" class="mb-4">조직 정보</flux:heading>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <flux:field>
                <flux:label>조직 이름</flux:label>
                <flux:input value="{{ $organization->name }}" readonly />
            </flux:field>
        </div>
        <div>
            <flux:field>
                <flux:label>대표자</flux:label>
                <flux:input value="{{ $organization->owner }}" readonly />
            </flux:field>
        </div>
        <div>
            <flux:field>
                <flux:label>사업자 번호</flux:label>
                <flux:input value="{{ $organization->business_register_number }}" readonly />
            </flux:field>
        </div>
        <div>
            <flux:field>
                <flux:label>대표 전화</flux:label>
                <flux:input value="{{ $organization->phone_number }}" readonly />
            </flux:field>
        </div>
        <div class="md:col-span-2">
            <flux:field>
                <flux:label>주소</flux:label>
                <flux:input value="{{ $organization->address }} {{ $organization->detail_address ?? '' }}" readonly />
            </flux:field>
        </div>
        @if($organization->postcode)
        <div>
            <flux:field>
                <flux:label>우편번호</flux:label>
                <flux:input value="{{ $organization->postcode }}" readonly />
            </flux:field>
        </div>
        @endif
        <div>
            <flux:field>
                <flux:label>등록일</flux:label>
                <flux:input value="{{ $organization->created_at->format('Y년 m월 d일') }}" readonly />
            </flux:field>
        </div>
    </div>
</div>
