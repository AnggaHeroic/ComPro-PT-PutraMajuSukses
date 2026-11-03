@props(['name' => 'q', 'value' => '', 'placeholder' => 'Cari...'])

<div class="relative min-w-0 flex-1 sm:w-72 sm:flex-none">
    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
    <x-form.input :name="$name" type="search" :value="$value" :placeholder="$placeholder" style="padding-left: 2.25rem" />
</div>
