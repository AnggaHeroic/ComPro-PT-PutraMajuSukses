@props(['variant' => 'primary', 'size' => 'md'])

{{--
    Tombol panel admin. variant: primary (aksi utama, bawaan type="submit") atau
    secondary (aksi pendukung, bawaan type="button"). Dengan atribut href jadi tautan <a>.
    Tombol situs publik ada di components/button.blade.php.
--}}
@php
    $tag = $attributes->has('href') ? 'a' : 'button';
    $sizes = ['md' => 'px-4 py-2.5 text-sm', 'sm' => 'px-3.5 py-1.5 text-[13px]'];

    $variants = [
        'primary' => 'rounded bg-primary text-white shadow-sm ring-1 ring-inset ring-black/10 hover:bg-primary-dark disabled:bg-gray-200 disabled:text-gray-400 disabled:shadow-none disabled:ring-gray-200 disabled:hover:bg-gray-200',
        'secondary' => 'rounded border border-gray-300 bg-white text-gray-700 shadow-sm hover:border-gray-400 hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none',
    ];
    $primary = $variant !== 'secondary';

    $defaults = ['class' => 'inline-flex items-center justify-center gap-2 font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 active:translate-y-px disabled:cursor-not-allowed '.$variants[$primary ? 'primary' : 'secondary'].' '.($sizes[$size] ?? $sizes['md'])];

    if ($tag === 'button') {
        $defaults['type'] = $primary ? 'submit' : 'button';
    }
@endphp

<{{ $tag }} {{ $attributes->merge($defaults) }}>
    {{ $slot }}
</{{ $tag }}>
