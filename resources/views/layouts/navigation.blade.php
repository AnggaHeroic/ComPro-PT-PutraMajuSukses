@php
    $user = Auth::user();
    $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1));
@endphp

{{-- Mobile backdrop --}}
<div
    x-show="sidebar"
    x-cloak
    x-transition.opacity
    @click="sidebar = false"
    class="fixed inset-0 z-40 bg-black/60 lg:hidden"
    aria-hidden="true"
></div>

<aside
    :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-hidden bg-linear-to-b from-secondary-dark to-[#222] text-white shadow-xl transition-transform duration-200 ease-out lg:shadow-none"
    aria-label="Navigasi admin"
    {{-- Tersembunyi sejak render pertama di layar kecil (lihat app.css), lalu dilepas setelah Alpine siap,
         sehingga sidebar tidak "muncul lalu meluncur menutup" setiap halaman dimuat. --}}
    data-pending
    x-init="$nextTick(() => $el.removeAttribute('data-pending'))"
>
    {{-- Dekorasi: sama seperti panel login (grid halus, watermark rumah) --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(to right, #fff 1px, transparent 1px), linear-gradient(to bottom, #fff 1px, transparent 1px); background-size: 40px 40px; mask-image: linear-gradient(to bottom, transparent 8%, #000 40%, transparent 95%); -webkit-mask-image: linear-gradient(to bottom, transparent 8%, #000 40%, transparent 95%);"></div>
    <svg class="pointer-events-none absolute -bottom-12 -right-12 h-60 w-60 text-white/[0.05]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>

    {{-- Brand --}}
    <div class="relative flex h-16 shrink-0 items-center justify-between border-b-[3px] border-primary bg-white px-5">
        <a href="{{ route('admin.dashboard') }}" aria-label="Dashboard">
            <x-application-logo class="h-9 w-auto mix-blend-multiply" />
        </a>
        <button type="button" @click="sidebar = false" aria-label="Tutup menu" class="rounded p-1.5 text-gray-500 hover:bg-gray-100 lg:hidden">
            <x-icon name="close" class="h-5 w-5" />
        </button>
    </div>

    {{-- Menu --}}
    <nav class="relative flex-1 space-y-7 overflow-y-auto px-3 py-6">
        <div>
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[0.14em] text-red-500">Menu</p>
            <div class="space-y-1">
                <x-admin.side-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <x-icon name="dashboard" class="h-5 w-5" stroke-width="1.6" />
                    Dashboard
                </x-admin.side-link>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[0.14em] text-red-500">Konten</p>
            <div class="space-y-1">
                <x-admin.side-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')">
                    <x-icon name="file-text" class="h-5 w-5" />
                    Konten Halaman
                </x-admin.side-link>
                <x-admin.side-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                    <x-icon name="layers" class="h-5 w-5" stroke-width="1.6" />
                    Kategori Usaha
                    @if (($draftCounts['categories'] ?? 0) > 0)
                        <span class="ml-auto rounded-sm bg-yellow-400 px-1.5 py-0.5 text-[10px] font-bold leading-none text-yellow-950" title="{{ $draftCounts['categories'] }} draft">{{ $draftCounts['categories'] }}</span>
                    @endif
                </x-admin.side-link>
                <x-admin.side-link :href="route('admin.activities.index')" :active="request()->routeIs('admin.activities.*')">
                    <x-icon name="list" class="h-5 w-5" stroke-width="1.6" />
                    Aktivitas Usaha
                    @if (($draftCounts['activities'] ?? 0) > 0)
                        <span class="ml-auto rounded-sm bg-yellow-400 px-1.5 py-0.5 text-[10px] font-bold leading-none text-yellow-950" title="{{ $draftCounts['activities'] }} draft">{{ $draftCounts['activities'] }}</span>
                    @endif
                </x-admin.side-link>
                <x-admin.side-link :href="route('admin.gallery.index')" :active="request()->routeIs('admin.gallery.*')">
                    <x-icon name="image" class="h-5 w-5" stroke-width="1.6" />
                    Galeri
                </x-admin.side-link>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[0.14em] text-red-500">Akun</p>
            <div class="space-y-1">
                <x-admin.side-link :href="route('admin.activity-log.index')" :active="request()->routeIs('admin.activity-log.*')">
                    <x-icon name="clock" class="h-5 w-5" stroke-width="1.6" />
                    Riwayat Aktivitas
                </x-admin.side-link>
                <x-admin.side-link :href="route('admin.profile.edit')" :active="request()->routeIs('admin.profile.*')">
                    <x-icon name="user" class="h-5 w-5" />
                    Profil
                </x-admin.side-link>
                <x-admin.side-link :href="route('home')" target="_blank" rel="noopener">
                    <x-icon name="external-link" class="h-5 w-5" />
                    Lihat Website
                </x-admin.side-link>
            </div>
        </div>
    </nav>

    {{-- User --}}
    <div class="relative shrink-0 border-t border-white/10 bg-black/30 p-3 backdrop-blur-sm">
        <div class="flex items-center gap-3 px-2 py-2">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white ring-2 ring-white/20">{{ $initial }}</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-1" data-no-confirm>
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none">
                <x-icon name="logout" class="h-5 w-5" />
                Keluar
            </button>
        </form>
    </div>
</aside>
