<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <div>
        <textarea
            wire:model="{{ $getStatePath() }}"
            class="w-full p-2 border rounded-md"
            placeholder='{"latitude": "12.345", "longitude": "67.890"}'
            rows="5"
        ></textarea>
        </div>
    </div>
</x-dynamic-component>
