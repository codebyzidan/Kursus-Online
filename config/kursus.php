<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pengaturan domain kursus
    |--------------------------------------------------------------------------
    */

    'currency' => 'IDR',

    // Persentase pendapatan instruktur dari harga bersih item (0.7 = 70%).
    'instructor_revenue_share' => (float) env('INSTRUCTOR_REVENUE_SHARE', 0.7),

    // Batas waktu pembayaran sebelum pesanan kedaluwarsa (menit).
    'order_expiry_minutes' => (int) env('ORDER_EXPIRY_MINUTES', 60),

    // Progres minimal (%) sebelum siswa boleh menulis ulasan.
    'review_min_progress' => (int) env('REVIEW_MIN_PROGRESS', 30),

    'certificate' => [
        'disk' => 'private',
        'path' => 'certificates',
    ],

    'invoice' => [
        'disk' => 'private',
        'path' => 'invoices',
    ],

    'video' => [
        // Umur signed URL video berproteksi (menit).
        'signed_url_ttl' => (int) env('VIDEO_SIGNED_URL_TTL', 30),
    ],

];
