# ComPro PT. Putra Maju Sukses

Situs profil perusahaan PT. Putra Maju Sukses beserta CMS admin untuk mengelola konten halaman, kategori usaha, aktivitas usaha, dan galeri. Dibangun dengan Laravel 13, Blade, Tailwind CSS v4, dan Alpine.js.

> **Status database.** Saat ini pengembangan dan pengujian lokal memakai **SQLite**. **MySQL 8** baru dipakai pada tahap deploy, dengan menjalankan `php artisan migrate` di server (lihat [Deploy ke server (MySQL)](#deploy-ke-server-mysql)). Kode, migrasi, dan seluruh tes sudah diuji di MySQL 8.

## Isi README

- [Fitur](#fitur)
- [Kebutuhan](#kebutuhan)
- [Instalasi](#instalasi)
- [Perintah sehari-hari](#perintah-sehari-hari)
- [Struktur proyek](#struktur-proyek)
- [Cara kerja singkat](#cara-kerja-singkat)
- [Konvensi kode](#konvensi-kode)
- [Panduan menambah fitur](#panduan-menambah-fitur)
- [Konfigurasi penting](#konfigurasi-penting)
- [Deploy ke server (MySQL)](#deploy-ke-server-mysql)
- [Pengujian](#pengujian)
- [Masalah yang sering muncul](#masalah-yang-sering-muncul)

## Fitur

**Situs publik**

| Alamat | Isi |
|---|---|
| `/` | Beranda |
| `/tentang-kami` | Profil perusahaan |
| `/layanan-jasa` | Daftar kategori usaha |
| `/layanan-jasa/{kategori}` | Aktivitas usaha dalam satu kategori |
| `/layanan-jasa/{kategori}/{aktivitas}` | Detail aktivitas beserta galeri foto |
| `/kontak-kami` | Kontak dan lokasi |

**CMS admin** (`/admin`, wajib masuk lewat `/login`)

| Menu | Fungsi |
|---|---|
| Dashboard | Ringkasan isi dan aktivitas terakhir |
| Konten Halaman | Mengubah teks, daftar, dan gambar di Beranda, Tentang Kami, Layanan & Jasa, dan Kontak Kami, dengan pratinjau per bagian dan setelan default |
| Kategori Usaha | Tambah, ubah, hapus, dan urutkan kategori; status Terbit atau Draf |
| Aktivitas Usaha | Tambah, ubah, hapus, dan urutkan aktivitas per kategori |
| Galeri | Foto dokumentasi tiap aktivitas (unggah banyak sekaligus, caption, urutan) |
| Riwayat Aktivitas | Catatan siapa mengubah apa dan kapan, dengan tab, filter, dan pencarian |
| Profil | Ubah nama, email, dan password admin |

Admin keluar otomatis bila tidak aktif (bawaan 30 menit, dengan peringatan 2 menit sebelumnya). Tiga kali salah memasukkan password mengunci percobaan berikutnya selama 10 detik.

## Kebutuhan

| Perangkat | Versi |
|---|---|
| PHP | 8.3 atau lebih baru (ekstensi `pdo_sqlite`, `pdo_mysql`, `mbstring`, `gd` dengan dukungan WebP) |
| Composer | 2.x |
| Node.js dan npm | Node.js 20.19 atau lebih baru |
| Database | SQLite untuk pengembangan lokal (sudah termasuk di PHP), MySQL 8 untuk deploy |

Pengguna Laragon sudah mendapat PHP, Composer, Node.js, dan MySQL sekaligus.

## Instalasi

### 1. Ambil kode dan pasang dependensi

```bash
git clone https://github.com/AnggaHeroic/ComPro-PT-PutraMajuSukses.git
cd ComPro-PT-PutraMajuSukses

composer install
npm install
```

### 2. Buat berkas `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Di Windows PowerShell, ganti `cp` dengan `copy`.

### 3. Siapkan database (SQLite untuk lokal)

Pengembangan lokal memakai SQLite, jadi tidak perlu menginstal atau menyalakan server database. `.env.example` sudah memilih SQLite (`DB_CONNECTION=sqlite`), jadi cukup buat berkas databasenya:

```bash
touch database/database.sqlite
```

Di Windows PowerShell: `New-Item database/database.sqlite`.

MySQL baru dipakai saat deploy ke server; langkah-langkahnya ada di bagian [Deploy ke server (MySQL)](#deploy-ke-server-mysql).

### 4. Atur akun admin

Isi data login admin di `.env`. Akun dibuat otomatis saat migrasi:

```env
ADMIN_NAME=Admin
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=ganti-dengan-password-anda
```

`ADMIN_EMAIL` dan `ADMIN_PASSWORD` wajib terisi. Jika kosong, migrasi berhenti dengan pesan yang jelas.

### 5. Jalankan migrasi

```bash
php artisan migrate
```

Selain membuat tabel, perintah ini mengisi akun admin dan katalog bawaan (5 kategori dan 14 aktivitas usaha). Bagian yang sudah ada tidak ditimpa, jadi aman dijalankan ulang.

> Data awal dipasang oleh migrasi `seed_initial_site_data`, bukan hanya oleh seeder. Karena itu `php artisan migrate` saja sudah cukup untuk mendapat situs yang terisi, baik di SQLite maupun MySQL.

### 6. Tautkan folder unggahan

```bash
php artisan storage:link
```

Foto galeri dan gambar halaman yang diunggah admin tersimpan di `storage/app/public`. Perintah ini membuatnya dapat diakses dari browser.

### 7. Jalankan aplikasi

Cara paling mudah, satu perintah untuk server Laravel dan Vite sekaligus:

```bash
composer run dev
```

Atau buka dua terminal:

```bash
# Terminal 1: aset (CSS dan JavaScript)
npm run dev

# Terminal 2: server Laravel
php artisan serve
```

Buka [http://localhost:8000](http://localhost:8000) untuk situs publik. Halaman login admin ada di [http://localhost:8000/login](http://localhost:8000/login), memakai `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dari langkah 4. Setelah masuk, dashboard ada di `/admin`.

Jika hanya ingin melihat hasilnya tanpa mengembangkan, jalankan `npm run build` sekali sebagai ganti `npm run dev`, lalu cukup `php artisan serve`.

## Perintah sehari-hari

| Perintah | Fungsi |
|---|---|
| `composer run dev` | Server Laravel dan Vite sekaligus |
| `npm run build` | Membuat aset produksi di `public/build` |
| `php artisan migrate` | Menjalankan migrasi baru |
| `php artisan test` | Menjalankan seluruh tes |
| `php artisan test --filter=NamaTes` | Menjalankan tes tertentu |
| `php artisan config:clear` | Membersihkan cache konfigurasi setelah mengubah `.env` |
| `php artisan view:clear` | Membersihkan cache Blade |
| `vendor/bin/pint` | Merapikan gaya kode PHP |

## Struktur proyek

```
app/
  Http/
    Controllers/        Controller publik (ServiceController), Auth/, dan Admin/ untuk CMS
    Middleware/         AdminIdleTimeout: keluar otomatis bila admin tidak aktif
    Requests/           Form Request: validasi tiap form, ada di Admin/
  Models/               ServiceCategory, BusinessActivity, ActivityImage, PageContent,
                        PageContentDefault, ActivityLog, User
  Observers/            Mencatat tambah, ubah, hapus model ke riwayat aktivitas
  Providers/            AppServiceProvider: view composer navbar dan footer, pencatat masuk/keluar
  Services/             ImageUploader: mengolah dan menyimpan gambar unggahan
  Support/              Helper tanpa status: StatusFilter, Reorder, SaveRedirect, Icons,
                        ActivityLogger, PageContents, Slug, IdleSession, dan lainnya
config/
  admin.php             Batas tidak aktif, akun admin awal, pembatasan login
  images.php            Aturan unggah gambar dan batas galeri
database/
  migrations/           Termasuk migrasi yang mengisi data awal situs
  seeders/              ServiceCatalogSeeder (5 kategori dan 14 aktivitas)
resources/
  css/app.css           Token tema (warna, font) dan utilitas CSS bersama
  js/
    app.js              Skrip situs publik (carousel)
    admin.js            Skrip panel admin dan halaman login
    lightbox.js         Lightbox foto, dipakai publik dan galeri admin
    unsaved-guard.js    Peringatan perubahan form yang belum disimpan
  views/
    layouts/            app (publik), admin, guest (login), error
    pages/, services/   Halaman situs publik
    admin/              Halaman CMS
    components/         Komponen Blade (lihat bagian Konvensi kode)
routes/web.php          Semua rute publik dan admin
tests/Feature/          Tes per fitur: Admin/, Auth/, Catalog/
```

Data disimpan di tujuh tabel utama:

| Tabel | Isi |
|---|---|
| `service_categories` | Kategori usaha |
| `business_activities` | Aktivitas usaha; tiap aktivitas milik satu kategori |
| `activity_images` | Foto galeri; tiap foto milik satu aktivitas |
| `page_contents` | Teks dan gambar halaman sebagai pasangan kunci dan nilai (`page`, `key`, `value`) |
| `page_content_defaults` | Setelan default yang ditetapkan admin untuk tiap halaman |
| `activity_logs` | Riwayat aktivitas admin |
| `users` | Akun admin |

## Cara kerja singkat

**Halaman publik.** Rute di `routes/web.php` menuju `ServiceController` atau langsung ke view. Data diambil lewat model dengan `published()` dan `ordered()`. Teks halaman (Beranda, Tentang Kami, dan seterusnya) dibaca dari `page_contents` lewat kelas `PageContents`, yang menyediakan nilai bawaan bila admin belum mengubahnya. View composer di `AppServiceProvider` mengirim daftar kategori ke navbar dan footer.

**Panel admin.** Rute di bawah `/admin` dilindungi middleware `auth` dan `admin.idle`. Alurnya: Form Request memvalidasi, controller memproses, model menyimpan, lalu Observer mencatat ke `activity_logs`.

**Gambar.** Semua unggahan lewat `ImageUploader`: diperiksa, diubah ke WebP, diperkecil bila terlalu lebar, lalu disimpan di disk `public` dengan nama UUID. Path yang tersimpan di database selalu relatif. File lama dihapus dengan `release()`, yang memeriksa dulu apakah masih dipakai tabel lain.

**Riwayat aktivitas.** Tambah, ubah, dan hapus Kategori dan Aktivitas Usaha dicatat otomatis oleh Observer, hanya bila ada admin yang login (seeder dan tes tidak mencatat). Urut ulang, galeri, konten halaman, profil, dan masuk/keluar dicatat manual lewat `ActivityLogger::record()`.

## Konvensi kode

- **Bahasa.** Teks yang dilihat pengguna memakai bahasa Indonesia. Nama kelas, method, dan variabel memakai bahasa Inggris. Komentar memakai bahasa Indonesia.
- **Istilah di antarmuka admin.** Gunakan "Alamat URL", bukan "slug".
- **Logika yang dipakai ulang** ditaruh di satu tempat, bukan disalin ke controller lain:
  - fungsi murni tanpa efek samping di `app/Support` (`StatusFilter`, `Reorder`, `SaveRedirect`);
  - logika yang menyentuh disk, database, atau konfigurasi di `app/Services` (`ImageUploader`).
- **Riwayat aktivitas model** dicatat lewat Observer, bukan di controller.
- **Komponen Blade.**
  - `<x-icon name="..."/>` untuk semua ikon. Bentuknya ada di `app/Support/Icons.php`.
  - `<x-button>` untuk tombol situs publik, `<x-admin.button variant="primary|secondary">` untuk panel admin.
  - `<x-form.input>`, `<x-form.textarea>`, `<x-form.select>`, `<x-form.label>`, `<x-form.error>` untuk formulir.
  - `components/site/` untuk bagian situs publik, `components/admin/` untuk panel admin (kolom form di `admin/fields/`).
- **Gaya Tailwind yang berulang** dijadikan utilitas di `resources/css/app.css` (`form-control`, `eyebrow`, `admin-thead`, dan lainnya). Jangan menyalin deretan kelas panjang ke banyak file. Hati-hati dengan selektor yang bergantung pada nama kelas literal (misalnya `[&_.shadow-sm]:...`), karena mengganti kelas dengan utilitas bisa membuatnya tidak lagi cocok.
- **Dua berkas JavaScript.** Kode situs publik di `app.js`, kode admin di `admin.js`. Jangan mencampurnya, supaya pengunjung tidak mengunduh kode admin.
- **Satu halaman, satu berkas.** Halaman panjang seperti `home` atau `dashboard` sengaja tidak dipecah. Pecah hanya bila bagian itu dipakai di lebih dari satu tempat atau punya logika sendiri.

## Panduan menambah fitur

**Menu admin baru**
1. Tambah rute di grup `admin` pada `routes/web.php`.
2. Buat controller di `app/Http/Controllers/Admin/` dan Form Request di `app/Http/Requests/Admin/`.
3. Buat view di `resources/views/admin/` dengan layout `<x-app-layout>`.
4. Tambah tautan di `resources/views/layouts/navigation.blade.php` dengan `<x-admin.side-link>`.
5. Tulis tes di `tests/Feature/Admin/`.

**Model baru yang dicatat riwayatnya**
1. Buat kelas turunan `App\Observers\LoggedModelObserver` (lihat `ServiceCategoryObserver`) yang mengisi `menu()`, `noun()`, `labels()`, dan bila perlu `details()`.
2. Tempelkan `#[ObservedBy(NamaObserver::class)]` pada model.

**Ikon baru.** Tambah satu entri di `app/Support/Icons.php`. Bila ikon itu boleh dipilih admin untuk kategori, daftarkan juga di `CategoryIcons::options()`.

**Gambar yang diunggah.** Pakai aturan `ImageUploader::rules()` di Form Request, lalu `$images->storeFromRequest(...)` di controller. Panggil `$images->release($pathLama)` setelah database diperbarui. Jangan menyimpan gambar dengan cara lain.

**Teks halaman yang bisa diubah admin.** Tambah bidangnya di `PageContents::definitions()` (`app/Support/PageContents.php`) beserta label, nilai bawaan, dan batas karakter. Formulir editor dan validasinya mengikuti definisi itu.

## Konfigurasi penting

Nilai diatur lewat `.env` (contoh lengkap di `.env.example`).

| Variabel | Fungsi |
|---|---|
| `DB_CONNECTION` | `sqlite` untuk pengembangan lokal, `mysql` di server (lihat bagian Deploy) |
| `APP_URL` | Alamat situs. Harus sama dengan alamat yang dibuka di browser, karena URL gambar dibuat darinya |
| `APP_DEBUG` | `true` hanya untuk pengembangan. Wajib `false` di server produksi |
| `SERVER_PORT` | Port `php artisan serve` (bawaan 8000) |
| `SESSION_COOKIE` | Nama cookie sesi. Beri nama berbeda tiap proyek bila beberapa proyek Laravel dijalankan di `localhost`, karena cookie dipakai bersama lintas port |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Akun admin awal |
| `ADMIN_IDLE_TIMEOUT`, `ADMIN_IDLE_WARNING` | Menit tidak aktif sebelum keluar otomatis, dan menit peringatan sebelumnya |

Batas unggah gambar ada di `config/images.php`: 5 MB per foto, 10 foto per unggahan, dan 24 foto per aktivitas. Semua gambar dikonversi ke WebP.

## Deploy ke server (MySQL)

Di server, situs memakai MySQL 8. Cukup menjalankan `php artisan migrate`: tabel dibuat, lalu akun admin dan katalog bawaan (5 kategori dan 14 aktivitas usaha berdasarkan data KBLI perusahaan) terpasang otomatis.

### 1. Siapkan database MySQL

Di phpMyAdmin (atau panel hosting) buat database kosong, misalnya `compro_pt1`, dengan collation `utf8mb4_unicode_ci`.

### 2. Isi `.env` di server

Ubah `DB_CONNECTION` menjadi `mysql` dan aktifkan (hapus tanda `#`) baris `DB_HOST` sampai `DB_PASSWORD` yang di `.env.example` berupa komentar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alamat-situs-anda.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=compro_pt1
DB_USERNAME=...
DB_PASSWORD=...

ADMIN_NAME=Admin
ADMIN_EMAIL=...        # email untuk login admin
ADMIN_PASSWORD=...     # password awal; ganti lewat menu Profil setelah login
```

`APP_DEBUG` wajib `false` di produksi, dan `APP_URL` harus sama dengan alamat situs karena URL gambar dibuat darinya. Lalu jalankan `php artisan config:clear` (jika memakai `php artisan serve`, matikan dan jalankan ulang).

### 3. Migrasi

```bash
php artisan migrate --force
```

Selain membuat tabel, migrasi `seed_initial_site_data` mengisi:

- akun admin dari `ADMIN_*` (jika `ADMIN_EMAIL` atau `ADMIN_PASSWORD` kosong, migrasi berhenti dengan pesan yang jelas; isi lalu jalankan lagi),
- kategori dan aktivitas usaha bawaan.

Hanya mengisi yang masih kosong: database yang sudah punya akun atau kategori tidak ditimpa, dan menjalankannya ulang tidak membuat data ganda. Konten halaman (Beranda, Tentang Kami, dan seterusnya) tidak perlu diisi; teks bawaannya tampil sampai admin mengubahnya.

### 4. Aset dan berkas unggahan

```bash
npm install
npm run build
php artisan storage:link
```

Foto yang diunggah admin tersimpan sebagai file (bukan di database). Bila ada yang ingin dibawa dari komputer pengembangan, salin folder `storage/app/public` ke server.

### Data di SQLite tidak ikut pindah

Migrasi hanya memindahkan **struktur** dan data awal. Perubahan yang sudah dibuat admin di SQLite lokal (teks halaman, kategori baru, riwayat, dan sebagainya) tidak otomatis ada di MySQL. Jika perlu dibawa, ekspor dan impor datanya secara terpisah sebelum deploy.

### Kembali ke SQLite

Ubah `DB_CONNECTION=sqlite` di `.env` dan jadikan komentar (`#`) baris `DB_DATABASE`, `DB_HOST`, dan seterusnya, lalu `php artisan config:clear`. Jika `DB_DATABASE` dibiarkan terisi, Laravel mencari berkas SQLite dengan nama itu.

## Pengujian

```bash
php artisan test
```

Tes memakai database SQLite di memori yang dibuat ulang tiap kali, jadi tidak menyentuh `database/database.sqlite` maupun MySQL. Tes juga memeriksa isi beberapa berkas JavaScript dan view, jadi ikut berubah bila kamu memindahkan kode antar berkas.

**Matikan `npm run dev` sebelum menjalankan tes.** Selama Vite dev server menyala, berkas `public/hot` ada dan halaman memuat aset dari server itu, sehingga puluhan tes gagal. Hentikan `npm run dev` (atau hapus `public/hot`), lalu jalankan tes.

## Masalah yang sering muncul

- **Halaman tampil tanpa gaya (CSS tidak termuat).** `npm run dev` belum jalan. Jalankan, atau hapus berkas `public/hot` lalu jalankan `npm run build`.
- **Perubahan `.env` tidak terbaca.** Jalankan `php artisan config:clear`. Jika memakai `php artisan serve` atau `composer run dev`, matikan servernya lalu jalankan ulang.
- **Galat `Database file at path [...] does not exist`.** Berkas SQLite belum dibuat, atau `DB_DATABASE` di `.env` masih berisi nama database MySQL. Buat `database/database.sqlite` dan jadikan `DB_DATABASE` komentar (lihat langkah 3).
- **Galat `SQLSTATE[HY000] [2002]` atau `Access denied`.** Pada MySQL: server belum menyala, atau `DB_*` di `.env` tidak cocok. Periksa nama database, pengguna, dan password, lalu `php artisan config:clear`.
- **Galat `could not find driver`.** Ekstensi PHP `pdo_sqlite` (lokal) atau `pdo_mysql` (deploy) belum aktif di `php.ini`.
- **Galat `No application encryption key has been specified`.** Jalankan `php artisan key:generate`.
- **Foto yang diunggah atau foto galeri tidak tampil.** Jalankan `php artisan storage:link`. Jika masih gagal, pastikan `APP_URL` sama dengan alamat yang dibuka di browser, dan tautan `public/storage` tidak menunjuk ke folder lama (hapus lalu buat ulang dengan `storage:link`).
- **Unggahan gambar ditolak dengan pesan "Gambar tidak dapat diproses".** Ekstensi PHP `gd` belum aktif atau tanpa dukungan WebP.
- **Admin terlempar ke halaman login saat membuka proyek Laravel lain di tab lain.** Cookie sesi bentrok. Atur `SESSION_COOKIE` yang berbeda di `.env` tiap proyek.
- **Banyak tes gagal serentak.** Lihat bagian [Pengujian](#pengujian): `npm run dev` masih menyala.
