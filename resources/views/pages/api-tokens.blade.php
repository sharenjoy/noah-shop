<x-filament::page>
    <form wire:submit.prevent="createToken">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            建立 Token
        </x-filament::button>
    </form>

    @if ($plainTextToken)
        <div class="mt-6 p-4 bg-green-100 rounded">
            <strong>Token（只會顯示一次）:</strong>
            <div class="break-all mt-2">
                {{ $plainTextToken }}
            </div>
        </div>
    @endif
</x-filament::page>
