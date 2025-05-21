<flux:modal name="create-organization" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('새 조직 추가') }}</flux:heading>
        </div>
        <flux:input label="Name" placeholder="Your name" />
        <flux:input label="Date of birth" type="date" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save changes</flux:button>
        </div>
    </div>
</flux:modal>
