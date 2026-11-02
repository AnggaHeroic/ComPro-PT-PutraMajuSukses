@php($editing = $category->exists)

<x-app-layout>
    <x-slot name="header">
        @if ($editing)
            <h1 class="font-display text-2xl font-bold text-gray-900">Edit Kategori</h1>
            <div class="mt-0.5 flex min-w-0 flex-col gap-0.5 lg:flex-row lg:items-center lg:gap-3">
                <p class="mt-0! min-w-0 text-sm text-gray-500">{{ $category->name }}</p>
                <x-admin.last-changed :subject="$category" :since="$category->updated_at" :created="$category->created_at" class="lg:border-l lg:border-gray-300 lg:pl-3" />
            </div>
        @else
            <h1 class="font-display text-2xl font-bold text-gray-900">Tambah Kategori</h1>
            <p class="mt-1 text-sm text-gray-500">Kategori mengelompokkan aktivitas usaha di halaman Layanan &amp; Jasa.</p>
        @endif
    </x-slot>

    <x-slot name="actions">
        @if ($editing)
            <x-admin.back :href="route('admin.categories.index')" />
        @else
            <x-admin.back :href="route('admin.categories.index')" />
        @endif
    </x-slot>

    <form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" data-unsaved-guard {{ collect($errors->getBags())->contains(fn ($bag) => $bag->any()) ? 'data-unsaved-dirty' : '' }}>
        @if ($editing)
            @method('PUT')
        @endif
        @csrf

        <div class="max-w-3xl space-y-6">
            <x-admin.card title="Informasi kategori" description="Nama dan deskripsi tampil di halaman Layanan & Jasa.">
                <div class="space-y-5">
                    <div>
                        <x-form.label for="name" value="Nama kategori" />
                        <x-form.input id="name" name="name" type="text" :value="old('name', $category->name)" required maxlength="120" autofocus />
                        <x-form.error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <x-admin.fields.url
                        prefix="/layanan-jasa/"
                        :value="$category->slug"
                        :exists="$category->exists"
                        :published="$category->is_published"
                    />

                    <div>
                        <x-form.label for="description" value="Deskripsi" />
                        <x-form.textarea id="description" name="description" rows="4" maxlength="2000">{{ old('description', $category->description) }}</x-form.textarea>
                        <x-form.error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Gambar" description="Gambar sampul kategori. Otomatis dikonversi ke WebP.">
                <x-admin.fields.image label="Gambar kategori" :current="$category->image_url" />
            </x-admin.card>

            <x-admin.card title="Ikon kartu di beranda" description="Pilih ikon dari daftar, atau unggah ikon sendiri. Ikon unggahan diutamakan bila ada.">
                <div class="space-y-6">
                    <x-admin.fields.icon-picker
                        name="icon"
                        :selected="old('icon', $category->exists ? $category->resolved_icon : \App\Support\CategoryIcons::DEFAULT)"
                        :options="\App\Support\CategoryIcons::options()"
                    />

                    <x-admin.fields.image
                        name="icon_image"
                        remove-name="remove_icon_image"
                        label="Ikon kustom (opsional)"
                        remove-label="Hapus ikon kustom"
                        :current="$category->icon_image_url"
                        :contain="true"
                        hint="PNG atau WebP transparan, disarankan persegi (mis. 256×256 px). SVG tidak diterima demi keamanan."
                    />
                </div>
            </x-admin.card>

            <x-admin.card title="Penayangan" description="Atur status tampil di website.">
                <div class="space-y-5">
                    <x-admin.fields.status :selected="old('status', $category->exists ? ($category->is_published ? 'published' : 'draft') : 'published')" />

                    <p class="text-xs text-gray-500">Urutan tampil diatur lewat tombol "Atur urutan" di halaman daftar.</p>
                </div>
            </x-admin.card>
        </div>

        <x-admin.form-actions :cancel="route('admin.categories.index')" />
    </form>
</x-app-layout>
