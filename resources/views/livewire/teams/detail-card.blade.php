<div class="bg-zinc-800 rounded-lg shadow-md p-6 mb-6">
    <flux:heading size="lg" class="mb-4">팀 정보</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <flux:field>
                <flux:label>소속 조직</flux:label>
                <flux:input value="{{ $organization->name }}" readonly="true"/>
            </flux:field>
        </div>
        <div></div>
        <div>
            <flux:field>
                <flux:label>설명</flux:label>
                <flux:input value="{{ $team->description }}" readonly="true"/>
            </flux:field>
        </div>
        <div>
            <flux:field>
                <flux:label>생성일</flux:label>
                <flux:input value="{{ $team->created_at->format('Y년 m월 d일') }}" readonly="true"/>
            </flux:field>
        </div>
    </div>
</div>
