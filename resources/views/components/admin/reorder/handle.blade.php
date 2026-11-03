@props(['label'])

<div class="inline-flex items-center gap-1">
    <span data-drag-handle class="flex h-10 w-10 cursor-grab touch-none items-center justify-center rounded text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 active:cursor-grabbing" aria-hidden="true" title="Seret untuk memindah">
        <x-icon name="grip" class="h-5 w-5" />
    </span>

    <button type="button" @click="move($event, -1)" aria-label="Naikkan {{ $label }}" title="Naikkan" class="flex h-7 w-7 items-center justify-center rounded border border-gray-300 bg-white text-gray-600 transition hover:border-primary hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
        <x-icon name="chevron-up" class="h-3.5 w-3.5" stroke-width="2.2" />
    </button>

    <button type="button" @click="move($event, 1)" aria-label="Turunkan {{ $label }}" title="Turunkan" class="flex h-7 w-7 items-center justify-center rounded border border-gray-300 bg-white text-gray-600 transition hover:border-primary hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
        <x-icon name="chevron-down" class="h-3.5 w-3.5" stroke-width="2.2" />
    </button>
</div>
