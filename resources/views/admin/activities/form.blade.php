@php($editing = $activity->exists)

<x-app-layout>
    <x-slot name="header">
        @if ($editing)
            <h1 class="font-display text-2xl font-bold text-gray-900">Edit Aktivitas</h1>
            <div class="header-meta">
                <p class="mt-0! min-w-0 text-sm text-gray-500">{{ $activity->name }}</p>
                <x-admin.last-changed :subject="$activity" :since="$activity->updated_at" :created="$activity->created_at" class="lg:border-l lg:border-gray-300 lg:pl-3" />
            </div>
        @else
            <h1 class="font-display text-2xl font-bold text-gray-900">Tambah Aktivitas</h1>
            <p class="mt-1 text-sm text-gray-500">Aktivitas usaha ditampilkan pada halaman kategori dan halaman detail.</p>
        @endif
    </x-slot>

    <x-slot name="actions">
        @if ($editing)
            <x-admin.button variant="secondary" size="sm" :href="route('admin.gallery.show', $activity)">Kelola galeri</x-admin.button>
            <x-admin.back :href="route('admin.activities.index', ['category' => $activity->service_category_id])" />
        @else
            <x-admin.back :href="route('admin.activities.index', array_filter(['category' => $activity->service_category_id]))" />
        @endif
    </x-slot>

    <form method="POST" action="{{ $editing ? route('admin.activities.update', $activity) : route('admin.activities.store') }}" enctype="multipart/form-data" data-unsaved-guard {{ collect($errors->getBags())->contains(fn ($bag) => $bag->any()) ? 'data-unsaved-dirty' : '' }}>
        @if ($editing)
            @method('PUT')
        @endif
        @csrf

        <div class="max-w-3xl space-y-6">
            <x-admin.card title="Informasi aktivitas" description="Kategori induk, nama, dan kode KBLI aktivitas usaha.">
                <div class="space-y-5">
                    <div>
                        <x-form.label for="service_category_id" value="Kategori" />
                        <x-form.select id="service_category_id" name="service_category_id" required class="w-full">
                            <option value="">— Pilih kategori —</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('service_category_id', $activity->service_category_id) === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.error :messages="$errors->get('service_category_id')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <x-form.label for="name" value="Nama aktivitas" />
                            <x-form.input id="name" name="name" type="text" :value="old('name', $activity->name)" required maxlength="255" />
                            <x-form.error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-form.label for="kbli_code" value="Kode KBLI" />
                            <x-form.input id="kbli_code" name="kbli_code" type="text" :value="old('kbli_code', $activity->kbli_code)" maxlength="10" placeholder="opsional" class="font-mono" />
                            <x-form.error :messages="$errors->get('kbli_code')" class="mt-2" />
                        </div>
                    </div>

                    <x-admin.fields.url
                        prefix="/layanan-jasa/…/"
                        :value="$activity->slug"
                        :exists="$activity->exists"
                        :published="$activity->is_published"
                    />
                </div>
            </x-admin.card>

            <x-admin.card title="Deskripsi" description="Teks singkat tampil di kartu kategori, teks detail tampil di halaman aktivitas.">
                <div class="space-y-5">
                    <div>
                        <x-form.label for="short_description" value="Deskripsi singkat (kartu di halaman kategori)" />
                        <x-form.textarea id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description', $activity->short_description) }}</x-form.textarea>
                        <x-form.error :messages="$errors->get('short_description')" class="mt-2" />
                    </div>

                    <div>
                        <x-form.label for="description" value="Deskripsi detail (halaman detail aktivitas)" />
                        <x-form.textarea id="description" name="description" rows="6" maxlength="10000">{{ old('description', $activity->description) }}</x-form.textarea>
                        <x-form.error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Gambar utama" description="Foto dokumentasi tambahan dikelola di menu Galeri.">
                <x-admin.fields.image label="Gambar utama" :current="$activity->image_url" />
            </x-admin.card>

            <x-admin.card title="Penayangan" description="Atur status tampil di website.">
                <div class="space-y-5">
                    <x-admin.fields.status :selected="old('status', $activity->exists ? ($activity->is_published ? 'published' : 'draft') : 'published')" />

                    <p class="text-xs text-gray-500">Urutan tampil diatur lewat tombol "Atur urutan" di halaman daftar.</p>
                </div>
            </x-admin.card>
        </div>

        <x-admin.form-actions :cancel="route('admin.activities.index', array_filter(['category' => $activity->service_category_id]))" />
    </form>
</x-app-layout>
