<div
    wire:ignore
    x-data="toastuiEditor(@entangle($attributes->wire('model')))"
    x-init="init()"
>
    <div x-ref="editor"></div>
    <textarea class="hidden" {{ $attributes->wire('model') }}></textarea>
</div>
