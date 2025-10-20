<?php

return [
    'hotel' => 'Hotel',
    'invoicing' => [
        'company_name' => 'Nama perusahaan',
        'company_address' => 'Alamat perusahaan',
        'company_email' => 'Email perusahaan',
        'company_phone' => 'Telepon perusahaan',
        'company_logo' => 'Logo perusahaan',
    ],
    'using_custom_font_for_invoice' => 'Menggunakan font kustom untuk faktur?',
    'invoice_font_family' => 'Keluarga font faktur (Hanya berfungsi untuk bahasa Latin)',
    'enable_invoice_stamp' => 'Aktifkan stempel faktur?',
    'invoice_support_arabic_language' => 'Mendukung bahasa Arab dalam faktur?',
    'invoice_code_prefix' => 'Awalan kode faktur',
    'invoice_settings' => 'Faktur',
    'invoice_settings_description' => 'Pengaturan informasi Faktur untuk penagihan',
    'general' => [
        'title' => 'Umum',
        'description' => 'Pengaturan umum untuk Hotel',
        'enable_booking' => 'Aktifkan pemesanan?',
        'maximum_number_of_guests' => 'Jumlah maksimum tamu',
        'minimum_number_of_guests' => 'Jumlah minimum tamu',
        'booking_number_format' => [
            'title' => 'Format Nomor Pemesanan (opsional)',
            'description' => 'Nomor pemesanan default dimulai pada angka tertentu. Anda dapat menyesuaikan angka awal dan akhir untuk nomor pemesanan. Misalnya, nomor pemesanan akan ditampilkan sebagai #:format.',
            'start_with' => 'Angka Awal',
            'end_with' => 'Angka Akhir',
        ],
        'booking_date_format' => 'Format Tanggal Pemesanan',
        'default_system_date_format' => 'Format tanggal sistem default',
        'enable_food_order' => 'Aktifkan pemesanan makanan?',
    ],
    'review' => [
        'title' => 'Ulasan',
        'description' => 'Pengaturan ulasan untuk Hotel',
        'enable_review_room' => 'Aktifkan ulasan?',
        'reviews_per_page' => 'Jumlah ulasan per halaman?',
    ],
    'currency' => [
        'title' => 'Mata Uang',
        'description' => 'Daftar mata uang yang digunakan di situs web',
    ],
    'invoice' => [
        'title' => 'Faktur',
        'description' => 'Pengaturan informasi Faktur untuk penagihan',
        'add_language_support' => 'Tambahkan dukungan bahasa',
        'only_latin_languages' => 'Hanya bahasa Latin',
        'confirm_reset' => 'Konfirmasi reset template faktur?',
        'confirm_message' => 'Apakah Anda benar-benar ingin mengatur ulang template faktur ini ke default?',
        'continue' => 'Lanjutkan',
    ],
    'invoice_template' => [
        'title' => 'Template Faktur',
        'description' => 'Pengaturan untuk template Faktur',
    ],
];
