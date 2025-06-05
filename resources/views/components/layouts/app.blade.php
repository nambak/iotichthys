<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
        <div class="text-center py-4">&copy; 2025 Unwanted</div>
    </flux:main>
</x-layouts.app.sidebar>
