<div class="mb-4">
    <flux:badge variant="subtle" class="flex items-center">
        <a href="{{ route('category.index') }}" class="hover:underline">카테고리</a>
        @foreach ($breadcrumbs as $index => $breadcrumb)
            <flux:icon.chevron-right class="size-4 mx-1" />
            @if ($index === count($breadcrumbs) - 1)
                <span class="font-medium">{{ $breadcrumb['name'] }}</span>
            @else
                <a href="{{ $breadcrumb['url'] }}" class="hover:underline">{{ $breadcrumb['name'] }}</a>
            @endif
        @endforeach
    </flux:badge>
</div>