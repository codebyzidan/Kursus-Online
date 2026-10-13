# saya ingin membuat webapp kursus onlinePRD — Platform Kursus Online

*Versi 1.0 · 13 Oktober 2026 · Stack: Laravel 12, Blade, MySQL*

## 1. Ringkasan Produk

Platform kursus online berbasis web yang memungkinkan instruktur membuat dan menjual kursus (video, materi bacaan, kuis), dan siswa membeli, mengikuti, serta menyelesaikan kursus sampai mendapatkan sertifikat. Admin mengelola seluruh platform: pengguna, kursus, transaksi, dan laporan.

Aplikasi dibangun sebagai **monolith server-rendered** (Laravel + Blade) agar cepat dikembangkan, mudah di-deploy, dan ramah SEO untuk halaman katalog kursus.

## 2. Tujuan dan Metrik Keberhasilan

**Tujuan**

- Menyediakan katalog kursus yang mudah dicari dan dibeli.
- Memberi pengalaman belajar yang jelas: progres, resume terakhir, kuis, sertifikat.
- Memberi instruktur alat sederhana untuk membuat kursus dan melihat pendapatan.
- Memberi admin kontrol penuh atas operasional dan keuangan.

**Metrik (target 3 bulan setelah rilis)**

| Metrik | Target |
| --- | --- |
| Konversi pengunjung → registrasi | ≥ 5% |
| Konversi registrasi → pembelian pertama | ≥ 8% |
| Completion rate kursus berbayar | ≥ 35% |
| Waktu muat halaman katalog (LCP) | < 2,5 detik |
| Pembayaran berhasil dari total percobaan | ≥ 90% |

## 3. Persona Pengguna

- **Siswa**: ingin menemukan kursus, membayar dengan mudah (transfer/VA/e-wallet), belajar dari HP maupun laptop, dan mendapat sertifikat.
- **Instruktur**: ingin mengunggah materi, mengatur harga, melihat jumlah siswa dan pendapatan, serta menjawab pertanyaan siswa.
- **Admin**: ingin memoderasi kursus, mengelola pengguna, memantau transaksi, dan mengatur kupon.

## 4. Ruang Lingkup

**Termasuk (MVP)**

- Autentikasi, profil, dan role (siswa, instruktur, admin)
- Katalog kursus, pencarian, filter, halaman detail
- Keranjang/checkout dan pembayaran online
- Ruang belajar (video, teks, lampiran, progres)
- Kuis sederhana dan sertifikat PDF
- Dashboard siswa, instruktur, dan admin
- Kupon diskon, ulasan dan rating
- Notifikasi email

**Di luar MVP (fase berikutnya)**

- Aplikasi mobile native
- Live class / webinar terintegrasi
- Forum komunitas penuh
- Langganan (subscription) dan paket bundle
- Multi-bahasa dan multi-mata uang
- Program afiliasi

## 5. Fitur dan Kebutuhan Fungsional

Prioritas: **P0** = wajib MVP, **P1** = penting, **P2** = nanti.

### 5.1 Autentikasi dan Akun (P0)

- Registrasi dan login dengan email + password; verifikasi email.
- Reset password via email.
- Login Google (P1).
- Profil: nama, foto, bio, ubah password.
- Role: `student`, `instructor`, `admin`. Pendaftaran sebagai instruktur melalui pengajuan yang disetujui admin.

### 5.2 Katalog dan Pencarian (P0)

- Daftar kursus dengan pagination, kategori, level (pemula/menengah/mahir), rentang harga, rating.
- Pencarian berdasarkan judul/deskripsi; urutkan (terbaru, terpopuler, harga, rating).
- Halaman detail: deskripsi, kurikulum (bagian dan materi), preview gratis, profil instruktur, ulasan, harga, tombol beli.
- URL ramah SEO (slug) dan meta tag.

### 5.2.1 Pembuatan Kursus oleh Instruktur (P0)

- Wizard: info dasar → kurikulum → harga → kirim untuk review.
- Struktur: Kursus → Bagian (section) → Materi (lesson). Tipe materi: video, teks, file lampiran.
- Urutkan bagian/materi dengan drag-and-drop.
- Status kursus: `draft`, `pending_review`, `published`, `rejected`, `archived`.
- Tandai materi sebagai *preview gratis*.

### 5.3 Checkout dan Pembayaran (P0)

- Beli langsung atau lewat keranjang; terapkan kupon.
- Pembayaran melalui payment gateway (Virtual Account, e-wallet, QRIS, kartu).
- Status pesanan: `pending`, `paid`, `expired`, `failed`, `refunded`.
- Webhook gateway memverifikasi pembayaran dan otomatis memberi akses (enrollment).
- Invoice/bukti pembayaran dikirim via email dan tersedia di dashboard.
- Kebijakan refund diatur admin (P1).

### 5.4 Ruang Belajar (P0)

- Pemutar video, konten teks, unduhan lampiran.
- Sidebar kurikulum dengan penanda materi selesai.
- Progres kursus (persentase) dan tombol "Lanjutkan belajar" ke materi terakhir.
- Hanya siswa yang sudah *enrolled* yang bisa mengakses materi berbayar.
- Catatan pribadi per materi (P2).

### 5.5 Kuis dan Sertifikat (P0/P1)

- Kuis pilihan ganda per bagian atau akhir kursus; skor lulus (passing grade) diatur instruktur.
- Batas percobaan dan penjelasan jawaban (P1).
- Sertifikat PDF otomatis saat progres 100% dan lulus kuis akhir; memiliki nomor unik dan halaman verifikasi publik (QR code).

### 5.6 Ulasan dan Interaksi (P1)

- Rating 1–5 dan ulasan oleh siswa yang sudah mengikuti kursus (minimal progres tertentu).
- Tanya-jawab per materi; instruktur dapat membalas.
- Moderasi ulasan oleh admin.

### 5.7 Dashboard Siswa (P0)

- Kursus saya, progres, sertifikat, riwayat pembelian, wishlist (P1).

### 5.8 Dashboard Instruktur (P0)

- Daftar kursus dan statusnya, jumlah siswa, pendapatan, rating.
- Laporan pendapatan per periode dan permintaan penarikan (payout) (P1).

### 5.9 Panel Admin (P0)

- Kelola pengguna, instruktur, kategori, kursus (setujui/tolak), pesanan, kupon.
- Statistik: pendapatan, pendaftar baru, kursus terlaris.
- Ekspor laporan ke Excel/CSV.
- Pengaturan situs: nama, logo, kontak, kredensial gateway, template email.

### 5.10 Notifikasi (P1)

Email untuk: verifikasi akun, konfirmasi pembayaran, pembayaran kedaluwarsa, kursus disetujui/ditolak, sertifikat terbit. Notifikasi in-app sederhana (P2).

## 6. Kebutuhan Non-Fungsional

- **Performa**: halaman publik di-cache; query dioptimalkan dengan eager loading dan indeks; LCP < 2,5 detik.
- **Keamanan**: proteksi CSRF, XSS (escaping Blade), SQL injection (Eloquent/prepared statements), rate limiting login, password di-hash (bcrypt/argon2), validasi tanda tangan webhook, otorisasi lewat Policy/Gate, file upload divalidasi (tipe dan ukuran).
- **Proteksi konten**: video hanya dapat diakses oleh siswa terdaftar (signed URL berumur pendek atau streaming terproteksi).
- **Ketersediaan**: target uptime 99,5%; backup database harian dan retensi minimal 7 hari.
- **Skalabilitas**: tugas berat (email, sertifikat, pemrosesan) lewat queue; storage file dipisah dari server aplikasi agar mudah dipindah ke object storage.
- **Responsif**: seluruh halaman nyaman di layar mobile; aksesibilitas dasar (kontras, label form, navigasi keyboard).
- **Observabilitas**: log terpusat dan pelacakan error.
- **Kepatuhan**: halaman kebijakan privasi dan syarat layanan; persetujuan saat registrasi; mengikuti prinsip perlindungan data pribadi (UU PDP).

## 7. Daftar Tech Stack

### 7.1 Inti

| Lapisan | Teknologi | Catatan |
| --- | --- | --- |
| Bahasa | PHP 8.3 | Versi yang didukung penuh Laravel 12 |
| Framework | Laravel 12 | Routing, Eloquent, queue, scheduler, validasi |
| Templating | Blade (+ Blade components) | Server-rendered, komponen UI dapat dipakai ulang |
| Database | MySQL 8.0 | Engine InnoDB, charset utf8mb4 |
| Dependency | Composer, Node.js LTS + npm |  |
| Build frontend | Vite | Bawaan Laravel |

### 7.2 Frontend

| Kebutuhan | Teknologi |
| --- | --- |
| CSS | Tailwind CSS v4 |
| Interaktivitas ringan | Alpine.js (dropdown, modal, tab, accordion kurikulum) |
| Komponen UI | Blade components buatan sendiri bergaya shadcn (kartu, tombol, tabel, badge, dialog) |
| Ikon | Lucide (via `blade-lucide-icons` atau SVG inline) |
| Grafik dashboard | Chart.js atau ApexCharts |
| Editor teks kaya (deskripsi/materi) | Tiptap atau Quill |
| Drag-and-drop kurikulum | SortableJS |
| Pemutar video | Plyr (atau video.js) |
| Interaksi tanpa reload (opsional) | Livewire 3 untuk pencarian live, keranjang, dan kuis; dipakai selektif, bukan di seluruh aplikasi |

### 7.3 Backend dan Paket Laravel

| Kebutuhan | Paket |
| --- | --- |
| Autentikasi scaffolding (Blade) | Laravel Breeze (stack Blade) |
| Login sosial | Laravel Socialite (Google) |
| Role dan permission | spatie/laravel-permission |
| Upload dan konversi media | spatie/laravel-medialibrary |
| URL slug | spatie/laravel-sluggable |
| Activity log | spatie/laravel-activitylog |
| Tabel data admin | yajra/laravel-datatables atau Livewire tables |
| Ekspor/impor Excel | maatwebsite/excel |
| Sertifikat dan invoice PDF | barryvdh/laravel-dompdf |
| QR code verifikasi sertifikat | simplesoftwareio/simple-qrcode |
| Pembayaran | Midtrans (midtrans/midtrans-php) atau Xendit SDK |
| Pencarian | Fulltext index MySQL pada MVP; Laravel Scout + Meilisearch bila katalog besar |
| Queue | Driver `database` pada MVP; Redis + Horizon saat trafik naik |
| Cache dan session | File/database pada MVP; Redis di produksi |

### 7.4 Penyimpanan dan Video

- **File dan gambar**: disk lokal pada tahap awal; S3-compatible storage (AWS S3, Cloudflare R2, atau MinIO) untuk produksi.
- **Video**: pilih salah satu (lihat Open Questions): (a) Bunny Stream / Vimeo / Cloudflare Stream dengan embed terproteksi, atau (b) file MP4/HLS sendiri dengan signed URL. Opsi (a) disarankan agar beban bandwidth tidak membebani server aplikasi.

### 7.5 Infrastruktur dan DevOps

| Kebutuhan | Teknologi |
| --- | --- |
| Web server | Nginx + PHP-FPM |
| Proses queue | Supervisor |
| Scheduler | Cron menjalankan `schedule:run` |
| Email | SMTP (Mailgun, Brevo, atau Amazon SES); Mailpit untuk lokal |
| SSL dan CDN | Let's Encrypt, Cloudflare |
| CI/CD | GitHub Actions (test + deploy) |
| Versi kontrol | Git |
| Monitoring error | Sentry atau Laravel Nightwatch |

### 7.6 Kualitas Kode

- **Testing**: Pest (unit dan feature test), Laravel Dusk untuk alur kritis (checkout) bila perlu.
- **Style**: Laravel Pint.
- **Analisis statis**: Larastan.
- **Debug lokal**: Laravel Telescope / Debugbar (hanya non-produksi).

## 8. Arsitektur Ringkas

- **Pola**: MVC dengan lapisan *Service/Action* untuk logika bisnis (mis. `EnrollStudent`, `ProcessPaymentWebhook`, `IssueCertificate`) agar controller tetap tipis.
- **Otorisasi**: Policy per model (Course, Order, Enrollment) + middleware role.
- **Event dan Listener**: `OrderPaid` → buat enrollment, kirim email, catat transaksi pendapatan instruktur.
- **Job di queue**: kirim email, buat PDF sertifikat, proses media.
- **Struktur modul**: Auth, Catalog, Course Builder, Checkout, Learning, Quiz, Certificate, Review, Admin.

## 9. Model Data (Garis Besar)

| Tabel | Kolom utama | Relasi |
| --- | --- | --- |
| `users` | name, email, password, avatar, bio, email\_verified\_at | punya role |
| `categories` | name, slug, parent\_id | punya banyak courses |
| `courses` | instructor\_id, category\_id, title, slug, description, level, price, thumbnail, status, published\_at | punya banyak sections |
| `sections` | course\_id, title, position | punya banyak lessons |
| `lessons` | section\_id, title, type, content/video\_url, duration, position, is\_preview |  |
| `attachments` | lesson\_id, file\_path, name |  |
| `orders` | user\_id, code, subtotal, discount, total, status, payment\_method, paid\_at, gateway\_ref | punya banyak order\_items |
| `order_items` | order\_id, course\_id, price |  |
| `coupons` | code, type, value, quota, used, valid\_from, valid\_until |  |
| `enrollments` | user\_id, course\_id, order\_id, enrolled\_at, completed\_at |  |
| `lesson_progress` | user\_id, lesson\_id, completed\_at, last\_position |  |
| `quizzes` | course\_id/section\_id, title, passing\_grade, max\_attempts | punya banyak questions |
| `questions`, `options` | quiz\_id, text, is\_correct |  |
| `quiz_attempts` | user\_id, quiz\_id, score, passed, finished\_at |  |
| `certificates` | enrollment\_id, number, issued\_at, file\_path |  |
| `reviews` | user\_id, course\_id, rating, body, is\_approved |  |
| `payouts` | instructor\_id, amount, status, requested\_at, paid\_at |  |
| `settings` | key, value |  |

Catatan: indeks pada `courses.slug`, `courses.status`, `enrollments(user_id, course_id)` (unik), `orders.code`, dan fulltext pada `courses(title, description)`.

## 10. Peta Halaman Utama

- **Publik**: Beranda, Katalog, Detail Kursus, Profil Instruktur, Verifikasi Sertifikat, Login/Registrasi.
- **Siswa**: Dashboard, Kursus Saya, Ruang Belajar, Kuis, Riwayat Pesanan, Sertifikat, Profil.
- **Instruktur**: Dashboard, Kursus (buat/edit), Kurikulum, Kuis, Siswa, Pendapatan, Payout.
- **Admin**: Dashboard, Pengguna, Instruktur, Kategori, Kursus (review), Pesanan, Kupon, Ulasan, Laporan, Pengaturan.

## 11. Arah UI/UX

- Dashboard bersih bernuansa terang dengan sidebar, kartu statistik, grafik, dan tabel, mengikuti gaya shadcn.
- Halaman publik mengutamakan kejelasan: kartu kursus konsisten, harga dan rating terlihat, CTA jelas.
- Ruang belajar fokus pada konten: video di tengah, kurikulum di sisi, navigasi sebelumnya/berikutnya.
- Mobile-first; dukung mode gelap sebagai P2.

## 12. Roadmap

| Fase | Durasi (estimasi) | Cakupan |
| --- | --- | --- |
| 0. Persiapan | 1 minggu | Setup repo, CI, desain sistem UI, skema database |
| 1. Fondasi | 2 minggu | Auth, role, profil, layout dashboard, kategori |
| 2. Kursus | 3 minggu | Course builder, kurikulum, katalog, detail, pencarian |
| 3. Transaksi | 2 minggu | Checkout, kupon, integrasi gateway, enrollment otomatis |
| 4. Belajar | 3 minggu | Ruang belajar, progres, kuis, sertifikat |
| 5. Admin dan laporan | 2 minggu | Panel admin, laporan, payout, ulasan |
| 6. Hardening | 2 minggu | Testing, optimasi, keamanan, UAT, deploy produksi |

Total estimasi MVP: sekitar 14–15 minggu untuk satu developer penuh waktu; dapat dipersingkat dengan menunda fitur P1/P2.

## 13. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Video diunduh/dibagikan tanpa bayar | Kehilangan pendapatan | Signed URL berumur pendek, embed terproteksi, watermark (P2) |
| Webhook pembayaran terlewat/duplikat | Siswa tidak dapat akses / akses ganda | Idempotency berdasarkan kode pesanan, verifikasi signature, job pengecekan status berkala |
| Bandwidth video membengkak | Biaya dan performa | Gunakan layanan video dan CDN terpisah |
| Scope membengkak | Jadwal molor | Disiplin prioritas P0/P1/P2, rilis MVP dulu |
| Penyalahgunaan konten instruktur | Reputasi | Alur review admin sebelum publish, fitur laporan |

## 14. Open Questions

1. Model bisnis: hanya kursus berbayar per kursus, atau juga gratis dan langganan?
2. Apakah instruktur multi-pihak (marketplace dengan bagi hasil) atau hanya satu pemilik konten?
3. Payment gateway yang dipilih: Midtrans atau Xendit (atau lainnya)?
4. Hosting video: layanan pihak ketiga atau self-hosted?
5. Apakah perlu live class, forum, atau fitur kohort di rilis awal?
6. Berapa target jumlah pengguna dan kursus pada tahun pertama (menentukan kebutuhan Redis dan Meilisearch)?
7. Apakah diperlukan faktur pajak / PPN pada invoice?
