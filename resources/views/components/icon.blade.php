@props(['name'])

{{--
    Ikon SVG. Bentuk dan ukuran bawaan tiap ikon ada di App\Support\Icons; kelas dan atribut
    dari pemanggil (mis. class="h-4 w-4") ditambahkan di atasnya.
--}}
@php
    $icon = \App\Support\Icons::get($name);

    $bag = $attributes->merge(
        $icon['attrs']
        + ['xmlns' => 'http://www.w3.org/2000/svg']
        + ($icon['aria'] ? ['aria-hidden' => 'true'] : [])
    );

    if ($icon['classMode'] === 'merge' || ($icon['classMode'] === 'fallback' && ! $attributes->has('class'))) {
        $bag = $bag->merge(['class' => $icon['class']]);
    }
@endphp
<svg {{ $bag }}>{!! $icon['body'] !!}</svg>
