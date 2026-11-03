<x-app-layout>
    <x-slot name="header">
        <h1 class="font-display text-2xl font-bold text-gray-900">Profil</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola informasi dan keamanan akun admin.</p>
    </x-slot>

    <x-slot name="actions">
        <x-admin.back :href="route('admin.dashboard')" />
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <x-admin.card>
            <section>
                <header>
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Profil</h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui nama dan alamat email akun admin.</p>
                </header>

                <form method="post" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-5" data-unsaved-guard {{ collect($errors->getBags())->contains(fn ($bag) => $bag->any()) ? 'data-unsaved-dirty' : '' }}>
                    @csrf
                    @method('patch')

                    <div>
                        <x-form.label for="name" value="Nama" />
                        <x-form.input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-form.error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-form.label for="email" value="Email" />
                        <x-form.input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-form.error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-admin.button>Simpan</x-admin.button>

                        @if (session('status') === 'profile-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2500)"
                                class="text-sm font-medium text-green-600"
                            >Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </section>
        </x-admin.card>

        <x-admin.card>
            <section>
                <header>
                    <h2 class="text-lg font-semibold text-gray-900">Ubah Password</h2>
                    <p class="mt-1 text-sm text-gray-500">Gunakan password yang panjang dan acak agar akun tetap aman.</p>
                </header>

                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5" data-unsaved-guard {{ collect($errors->getBags())->contains(fn ($bag) => $bag->any()) ? 'data-unsaved-dirty' : '' }}>
                    @csrf
                    @method('put')

                    <div>
                        <x-form.label for="update_password_current_password" value="Password saat ini" />
                        <x-form.input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
                        <x-form.error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-form.label for="update_password_password" value="Password baru" />
                        <x-form.input id="update_password_password" name="password" type="password" autocomplete="new-password" />
                        <x-form.error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-form.label for="update_password_password_confirmation" value="Konfirmasi password baru" />
                        <x-form.input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                        <x-form.error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-admin.button>Simpan</x-admin.button>

                        @if (session('status') === 'password-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2500)"
                                class="text-sm font-medium text-green-600"
                            >Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </section>
        </x-admin.card>
    </div>
</x-app-layout>
