# Kursus Online

Platform kursus online (monolith server-rendered) berbasis **Laravel 12 + Blade**, mengikuti `prd.md`.
Dokumen ini merangkum setup dan bagian backend yang sudah dibangun.

## Ringkasan Stack

| Lapisan | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP **target 8.3**, minimum `^8.3`) |
| CSS | Tailwind CSS **v4** (plugin `@tailwindcss/vite`) |
| Komponen UI | **Basecoat UI** (`basecoat-css`) + komponen Blade `ui/` |
| Ikon | Lucide (`mallardduck/blade-lucide-icons`) |
| Font | Geist Variable (`@fontsource-variable/geist`) |
| Interaksi | Alpine.js (+focus/collapse/persist/mask) + htmx |
| Chart | Chart.js 4 (warna dari CSS variable) |
| Tanggal / Select / DnD | Flatpickr (`id`) · Tom Select · SortableJS |
| Validasi bahasa Indonesia | `laravel-lang/lang` (`lang/id`) |
| Auth | Laravel Breeze (stack Blade) + login Google (Socialite) |
| Role & izin | `spatie/laravel-permission` ^8.3 |
| Slug | `spatie/laravel-sluggable` ^4.0 |
| Media/file | `spatie/laravel-medialibrary` ^11.23 |
| Activity log | `spatie/laravel-activitylog` ^4.12 |
| Export Excel/CSV | `maatwebsite/excel` ^4.0 |
| PDF (sertifikat, invoice) | `barryvdh/laravel-dompdf` ^3.1 |
| QR verifikasi sertifikat | `simplesoftwareio/simple-qrcode` ^4.2 |
| Pembayaran | Midtrans (`midtrans/midtrans-php` ^2.6) |
| Testing | Pest ^4.7 |
| Style | Laravel Pint |
| Analisis statis | Larastan ^3.13 |

## Kebutuhan Sistem

- PHP **8.3+** (dikembangkan & diuji di 8.5)
- Ekstensi PHP: `pdo_sqlite`/`pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `fileinfo`, **`gd`**, **`exif`**
- Composer 2, Node.js LTS + npm
- Database: SQLite (lokal) / MySQL 8.0 (produksi)

> `gd` dan `exif` wajib karena `simple-qrcode`, `phpspreadsheet`, dan `medialibrary`. Di Debian/Ubuntu:
> `sudo apt install php8.3-gd php8.3-exif` (atau paket setara di distro lain).

## Kompatibilitas PHP 8.3

`composer.json` memuat:

```json
"require": { "php": "^8.3" },
"config": { "platform": { "php": "8.3.28" } }
```

Pin `platform.php` memaksa Composer memilih versi paket yang kompatibel 8.3 meski mesin lokal memakai 8.5.
Dua paket sengaja **diturunkan** dari versi terbaru (yang menuntut PHP `^8.4`):

| Paket | Versi terbaru (butuh 8.4) | Dipakai |
|---|---|---|
| `spatie/laravel-activitylog` | v5 | **v4.12** |
| `pestphp/pest` | v5 | **v4.7** |

Perbarui `composer.lock` hanya di mesin dengan pin di atas agar hasil tetap dapat dideploy ke 8.3.

> Catatan lingkungan pengembangan: bila `gd`/`exif` tersedia sebagai modul shared tapi belum aktif,
> aktifkan lewat direktori scan lokal tanpa mengubah `/etc/php/conf.d`:
> ```bash
> mkdir -p ~/.config/php/conf.d
> printf 'extension=gd\nextension=exif\n' > ~/.config/php/conf.d/30-kursus.ini
> export PHP_INI_SCAN_DIR=~/.config/php/conf.d
> ```

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# Lokal (SQLite): buat file database lalu migrasi + seed
touch database/database.sqlite
php artisan migrate:fresh --seed

npm install
npm run build
php artisan serve
```

Untuk produksi MySQL 8.0, ubah blok `DB_*` pada `.env` dan set `MEDIA_DISK`, kredensial `MIDTRANS_*`,
serta `GOOGLE_*`.

## Akun Demo (hasil seed)

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@kursus.test` | `password` |
| Instruktur | `rani@kursus.test` | `password` |
| Instruktur | `bagus@kursus.test` | `password` |
| Siswa | `dewi@kursus.test` | `password` |
| Siswa | `ahmad@kursus.test` | `password` |

## Struktur Backend

```
app/
├── Actions/            # satu action = satu use case
│   ├── Checkout/       # PlaceOrder, ProcessPaymentWebhook
│   ├── Course/         # CreateCourse, SubmitCourseForReview, Approve/RejectCourse
│   ├── Instructor/     # ApplyAsInstructor, Approve/RejectInstructorApplication
│   ├── Learning/       # EnrollStudent, RecordLessonProgress, CompleteCourse
│   ├── Payout/         # RequestPayout
│   ├── Quiz/           # SubmitQuizAttempt
│   └── Review/         # SubmitReview
├── Enums/              # UserRole, CourseStatus, LessonType, OrderStatus, ...
├── Events/ Listeners/  # OrderPaid, CourseApproved, CertificateIssued, PayoutRequested, ...
├── Jobs/               # GenerateCertificatePdfJob, GenerateInvoicePdfJob
├── Models/             # 22 model domain
├── Notifications/      # email + in-app (database)
├── Policies/           # Course, Lesson, Order, Enrollment, Review, Payout, ...
├── Services/
│   ├── Payment/        # PaymentGateway (interface) + MidtransGateway
│   ├── CertificateService, InvoiceService, CouponService
│   └── InstructorEarningsService
└── Console/Commands/   # ExpireUnpaidOrders (dijadwalkan tiap 10 menit)
```

### Alur kunci

- **Checkout → akses**: `PlaceOrder` membuat pesanan + item (bagi diskon proporsional) → `MidtransGateway`
  membuat Snap token → webhook divalidasi tanda tangannya di `ProcessPaymentWebhook` (idempotent) →
  event `OrderPaid` → listener membuat `Enrollment`, mengirim notifikasi, dan membuat invoice PDF.
- **Belajar → sertifikat**: `RecordLessonProgress` memperbarui progres; saat 100% `CompleteCourse`
  menerbitkan sertifikat (`CertificateService`) → event `CertificateIssued` → job membuat PDF + QR.
- **Review kursus**: `SubmitCourseForReview` (validasi kurikulum) → `ApproveCourse`/`RejectCourse` oleh admin.
- **Instruktur**: `ApplyAsInstructor` → `ApproveInstructorApplication` memberi role `instructor`.
- **Payout**: `RequestPayout` memvalidasi saldo dari `InstructorEarningsService`.

## Frontend (UI)

Frontend mengikuti `AGENTS.md`: Tailwind v4 + Basecoat, komponen Blade `resources/views/components/ui/`,
layout `layouts/app` (sidebar + topbar) dan `layouts/guest` (publik), plus `layouts/auth`.

- CSS: `resources/css/app.css` (`tailwindcss` → `basecoat-css` → `theme.css`), token di `theme.css`
  (light + `.dark`), tanpa hex di view.
- JS: `resources/js/app.js` + `resources/js/modules/` (charts, datepicker, tom-select, sortable, toaster).
  Alpine untuk state UI, htmx untuk pencarian/filter/paginasi (CSRF via `htmx:configRequest`,
  `window.basecoat.initAll()` dipanggil ulang setelah swap).
- Dark mode: toggle Basecoat + `localStorage` (`theme-script` mencegah kedip saat memuat).
- Ikon: `<x-lucide-nama />`; chart membaca warna dari CSS variable lewat `getComputedStyle`.
- Aksesibilitas & UX sesuai `AGENTS.md` §3: label, `@error`, fokus field error, tombol nonaktif saat
  submit, toast Basecoat, skeleton, empty state, tabel server-side, breadcrumb, target sentuh ≥36px.

## Perintah Berguna

```bash
php artisan migrate:fresh --seed   # reset + isi data demo
php artisan orders:expire          # tandai pesanan kedaluwarsa (terjadwal)
npm run build                      # kompilasi aset (Tailwind v4 + Vite)
npm run dev                        # vite dev server
php artisan test                   # Pest (66 test)
./vendor/bin/pint                  # format kode
./vendor/bin/phpstan analyse       # Larastan (level 5, tanpa error)
```

## Status & Langkah Berikutnya

Selesai: **Phase A–D (fondasi + domain)** dan **Phase E/F (controller, route, dan UI lengkap)**
untuk publik, siswa, instruktur, dan admin — termasuk ekspor laporan Excel dan verifikasi sertifikat.

Berikutnya yang dapat dilanjutkan:

- Integrasi video berproteksi (signed URL / layanan pihak ketiga) pada ruang belajar.
- Unggah thumbnail/foto profil lewat form (media library sudah siap, belum ada input di beberapa form).
- Fitur P1/P2: notifikasi in-app, forum/Q&A per materi, wishlist lanjutan, laporan lanjutan, multi-bahasa.
- Uji beban/optimasi (eager loading & cache halaman publik) dan CI (GitHub Actions).

