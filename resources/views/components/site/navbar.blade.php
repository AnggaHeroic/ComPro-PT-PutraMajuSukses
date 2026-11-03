@php
    $isHomeActive = request()->routeIs('home');
    $isAboutActive = request()->routeIs('about');
    $isContactActive = request()->routeIs('contact');
    $isServicesActive = request()->routeIs('services*');

    // Tautan biasa di menu desktop dan mobile. Menambah halaman baru cukup menambah satu baris di sini.
    $links = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => $isHomeActive],
        ['label' => 'Tentang Kami', 'route' => 'about', 'active' => $isAboutActive],
    ];
    
    $activeCategorySlug = request()->routeIs('services.category', 'services.detail')
        ? request()->route('category')
        : null;

    $serviceCategories = $navCategories
        ->values()
        ->map(fn ($category, $index) => [
            'name' => $category->name,
            'slug' => $category->slug,
            'number' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
        ])
        ->all();

    // Dropdown desktop: <= 6 kategori satu kolom; > 6 dua kolom (maks. 12) + tautan ke semua.
    $manyCategories = count($serviceCategories) > 6;
    $dropdownCategories = array_slice($serviceCategories, 0, 12);
    $hiddenCategories = max(0, count($serviceCategories) - 12);
@endphp

<header{!! $siteInfo->mark('identity') !!}
    x-data="{
        open: false,
        servicesOpen: {{ request()->routeIs('services.category', 'services.detail') ? 'true' : 'false' }},
        desktopServicesOpen: false
    }"

    @keydown.escape.window="
        open = false;
        servicesOpen = false;
        desktopServicesOpen = false;
    "
    class="sticky top-0 z-50 border-b border-slate-200 bg-white"
>
    <nav
        @class(['container-site', 'relative' => auth()->check()])
        aria-label="Navigasi utama"
    >
        {{-- Dashboard Admin (hanya admin login). Absolute: berada di ruang kosong tepat di kanan
             tombol Kontak Kami (tepi kanan konten + jarak kecil), tanpa menggeser menu apa pun. --}}
        @auth
            <a
                href="{{ route('admin.dashboard') }}"
                title="Kembali ke Dashboard Admin"
                aria-label="Kembali ke Dashboard Admin"
                class="font-display absolute left-[calc(100%-2rem+0.25rem)] top-10 hidden h-8 w-7 -translate-y-1/2 items-center justify-center gap-1.5 rounded-md bg-slate-100 text-xs font-semibold text-slate-600 transition-colors duration-200 hover:bg-slate-200 hover:text-primary lg:inline-flex min-[1380px]:left-[calc(100%-2rem+0.75rem)] 2xl:w-auto 2xl:px-2.5"
            >
                <x-icon name="dashboard" class="h-3.5 w-3.5 shrink-0" />
                <span class="hidden 2xl:inline">Admin</span>
            </a>
        @endauth


        <div class="flex h-20 items-center justify-between">

            {{-- Logo --}}
            <a
                href="{{ route('home') }}"
                class="shrink-0"
                aria-label="PT. Putra Maju Sukses - Beranda"
            >
                <img
                    src="{{ $siteInfo->image('company_logo') }}"
                    alt="{{ $siteInfo('company_logo_alt') }}"
                    class="h-12 w-auto"
                >
            </a>


            {{-- Desktop Navigation --}}
            <div class="hidden items-center gap-8 md:flex">

                @foreach ($links as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="font-display relative py-2 text-sm font-medium transition-colors duration-200
                        {{ $link['active']
                            ? 'text-primary'
                            : 'text-slate-600 hover:text-primary' }}"
                    >
                        {{ $link['label'] }}

                        @if ($link['active'])
                            <span class="absolute inset-x-0 -bottom-1 h-0.5 rounded-full bg-primary"></span>
                        @endif
                    </a>
                @endforeach

                {{-- Layanan & Jasa --}}
                <div
                    class="relative"
                    @click.outside="desktopServicesOpen = false"
                >
                    <button
                        type="button"
                        @click="desktopServicesOpen = !desktopServicesOpen"
                        :aria-expanded="desktopServicesOpen.toString()"
                        aria-controls="desktop-services"
                        class="font-display relative inline-flex items-center gap-2 py-2 text-sm font-medium transition-colors duration-200
                        {{ $isServicesActive
                                ? 'text-primary'
                                : 'text-slate-600 hover:text-primary' }}"
                    >
                        <span class="relative py-2">
                            Layanan & Jasa
                            @if($isServicesActive)
                                <span class="absolute inset-x-0 -bottom-1 h-0.5 rounded-full bg-primary"></span>
                            @endif
                        </span>
                        <x-icon name="arrow-down"
                            class="transition-transform duration-200"
                            ::class="{ 'rotate-180': desktopServicesOpen }"
                        />
                    </button>

                    {{-- Desktop Services Dropdown --}}
                    <div
                        id="desktop-services"
                        x-show="desktopServicesOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        @class([
                            'absolute right-0 top-full z-50 mt-4',
                            'w-72' => ! $manyCategories,
                            'w-[34rem] max-w-[calc(100vw-2rem)]' => $manyCategories,
                            'border border-slate-200 bg-white shadow-lg',
                        ])
                    >
                        <div class="p-3">

                            {{-- View All --}}
                            <a
                                href="{{ route('services') }}"
                                class="group flex items-center justify-between border-b border-slate-200 px-4 py-3.5"
                            >
                                <span
                                    class="font-display text-xs font-bold uppercase tracking-[0.14em]
                                    {{ request()->routeIs('services')
                                        ? 'text-primary'
                                        : 'text-slate-500 group-hover:text-primary' }}"
                                >
                                    Semua Layanan
                                </span>

                                <span
                                    class="text-primary transition-transform duration-200 group-hover:translate-x-1"
                                    aria-hidden="true"
                                >
                                    →
                                </span>
                            </a>

                            @if ($manyCategories)
                                {{-- Dua kolom, dibaca turun ke bawah --}}
                                <div class="columns-2 gap-x-2 [column-rule:1px_solid_rgb(226_232_240)]">
                            @endif
                                @foreach ($manyCategories ? $dropdownCategories : $serviceCategories as $category)
                                    <a
                                        href="{{ route('services.category', $category['slug']) }}"
                                        @class([
                                            'group flex items-center justify-between',
                                            'break-inside-avoid gap-3 px-4 py-2.5' => $manyCategories,
                                            'px-4 py-3' => ! $manyCategories,
                                        ])
                                        @if ($activeCategorySlug === $category['slug'])
                                            aria-current="page"
                                        @endif
                                    >
                                        <span
                                            @class([
                                                'font-display text-sm font-medium',
                                                'leading-snug' => $manyCategories,
                                                'text-primary' => $activeCategorySlug === $category['slug'],
                                                'text-secondary-dark group-hover:text-primary' => $activeCategorySlug !== $category['slug'],
                                            ])
                                        >
                                            {{ $category['name'] }}
                                        </span>

                                        @if ($activeCategorySlug === $category['slug'])
                                            <span
                                                class="{{ $manyCategories ? 'h-1.5 w-1.5 shrink-0 rounded-full bg-primary' : 'h-1.5 w-1.5 rounded-full bg-primary' }}"
                                                aria-hidden="true"
                                            ></span>
                                        @endif
                                    </a>
                                @endforeach
                            @if ($manyCategories)
                                </div>
                            @endif

                            @if ($hiddenCategories > 0)
                                <a
                                    href="{{ route('services') }}"
                                    class="group mt-2 flex items-center justify-between border-t border-slate-200 px-4 py-3.5"
                                >
                                    <span class="font-display text-xs font-bold uppercase tracking-[0.14em] text-slate-500 group-hover:text-primary">
                                        Lihat semua {{ count($serviceCategories) }} bidang usaha
                                    </span>

                                    <span class="text-primary transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true">
                                        →
                                    </span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kontak Kami --}}
                <a
                    href="{{ route('contact') }}"
                    class="font-display inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition duration-200 hover:bg-primary-dark"
                >
                    Kontak Kami
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button
                type="button"
                @click="
                    open = !open;
                    if (!open) servicesOpen = false;
                "
                :aria-expanded="open.toString()"
                aria-controls="mobile-navigation"
                :aria-label="open ? 'Tutup menu navigasi' : 'Buka menu navigasi'"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-secondary-dark transition hover:border-primary hover:text-primary md:hidden"
            >
                {{-- Hamburger --}}
                <x-icon name="menu"
                    x-show="!open"
                    x-cloak
                />

                {{-- Close --}}
                <x-icon name="close"
                    x-show="open"
                    x-cloak
                    class="h-5 w-5"
                />
            </button>
        </div>

        {{-- Mobile Navigation --}}
        <div
            id="mobile-navigation"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="max-h-[calc(100dvh-5rem)] overflow-y-auto overscroll-contain border-t border-slate-100 py-4 md:hidden"
        >
            <div class="flex flex-col">

                @foreach ($links as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        @click="open = false"
                        class="font-display border-b border-slate-100 px-4 py-4 text-sm font-medium transition-colors
                        {{ $link['active']
                            ? 'text-primary'
                            : 'text-slate-600 hover:text-primary' }}"
                    >
                        <div class="flex items-center justify-between">
                            <span>
                                {{ $link['label'] }}
                            </span>

                            @if ($link['active'])
                                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                            @endif
                        </div>
                    </a>
                @endforeach

                {{-- Layanan & Jasa --}}
                <div class="border-b border-slate-100">
                    <button
                        type="button"
                        @click="servicesOpen = !servicesOpen"
                        :aria-expanded="servicesOpen.toString()"
                        aria-controls="mobile-services"
                        class="font-display flex w-full items-center justify-between px-4 py-4 text-left text-sm font-medium transition-colors
                        {{ $isServicesActive
                            ? 'text-primary'
                            : 'text-slate-600 hover:text-primary' }}"
                    >

                        <div class="flex items-center justify-between gap-4">
                            <span>
                                Layanan & Jasa
                            </span>
                            {{-- Active indicator for main services page --}}
                            @if(request()->routeIs('services'))
                                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                            @endif
                        </div>
                        <x-icon name="arrow-down"
                            class="transition-transform duration-200"
                            ::class="{ 'rotate-180': servicesOpen }"
                        />
                    </button>
                    
                    {{-- Business Categories --}}
                    <div
                        id="mobile-services"
                        x-show="servicesOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="overflow-hidden"
                    >
                        <div class="ml-4 border-l-2 border-primary/20 pb-3 pl-5">
                            @foreach ($serviceCategories as $category)
                                <a
                                    href="{{ route('services.category', $category['slug']) }}"
                                    @click="
                                        open = false;
                                        servicesOpen = false;
                                    "
                                    class="group flex items-center justify-between py-3"
                                    @if ($activeCategorySlug === $category['slug'])
                                        aria-current="page"
                                    @endif
                                >
                                    <div class="flex items-center gap-4">
                                        <span class="font-display text-xs font-semibold text-primary">
                                            {{ $category['number'] }}
                                        </span>

                                        <span
                                            class="font-display text-sm font-medium transition-colors
                                            {{ $activeCategorySlug === $category['slug']
                                                ? 'text-primary'
                                                : 'text-secondary-dark group-hover:text-primary' }}"
                                        >
                                            {{ $category['name'] }}
                                        </span>
                                    </div>

                                    @if ($activeCategorySlug === $category['slug'])
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-primary"
                                            aria-hidden="true"
                                        ></span>
                                    @endif
                                </a>
                            @endforeach

                            {{-- View All --}}
                            <a
                                href="{{ route('services') }}"
                                @click="
                                    open = false;
                                    servicesOpen = false;
                                "
                                class="font-display mt-2 inline-flex items-center gap-2 py-2 text-xs font-semibold text-primary"
                            >
                                Lihat semua layanan
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Dashboard Admin (hanya terlihat saat admin login) --}}
                @auth
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="font-display mt-4 flex items-center gap-2 border-t border-slate-100 px-4 py-4 text-xs font-medium text-slate-500 transition-colors hover:text-primary"
                    >
                    <x-icon name="dashboard" class="h-3.5 w-3.5" />
                        Dashboard Admin
                    </a>
                @endauth

                {{-- Contact --}}
                <a
                    href="{{ route('contact') }}"
                    @click="open = false"
                    class="font-display group mt-4 flex items-center justify-between border-t border-b border-primary px-4 py-5 text-sm font-semibold text-primary transition-colors hover:bg-primary hover:text-white"
                >
                    <span>
                        Kontak Kami
                    </span>
                    <span
                        class="text-lg transition-transform duration-200 group-hover:translate-x-1"
                        aria-hidden="true"
                    >
                        →
                    </span>
                </a>
            </div>
        </div>
    </nav>
</header>