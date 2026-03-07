# UMS - Sistem Rating Tenaga Kependidikan Universitas

Sistem web sederhana untuk menampilkan daftar tenaga kependidikan universitas dengan fitur rating, komentar, dan ulasan berbintang. Pengguna dapat menambahkan ulasan baru langsung dari website.

## Fitur Utama

- ✅ Daftar tenaga kependidikan dengan informasi lengkap
- ✅ Sistem rating dengan bintang (1-5)
- ✅ Komentar dan ulasan dari mahasiswa/staff
- ✅ Tampilan responsif dan modern
- ✅ Database MySQL untuk penyimpanan data

## Struktur File

```
ratingums/
├── database.sql      # Skema database dan data sampel
├── config.php        # Konfigurasi koneksi database
├── index.php         # Halaman utama aplikasi
├── style.css         # Styling untuk tampilan
└── README.md         # Dokumentasi ini
```

## Persyaratan Sistem

- PHP 7.0 atau lebih tinggi
- MySQL/MariaDB
- Web server (Apache/Nginx) atau XAMPP/WAMP

## Instalasi dan Setup

### 1. Setup Database

1. Buka phpMyAdmin atau MySQL client
2. Buat database baru dengan nama `ratingums`
3. Import file `database.sql` ke dalam database tersebut

### 2. Konfigurasi Database

Edit file `config.php` jika diperlukan:
```php
$host = 'localhost';     // Host database
$user = 'root';          // Username database
$password = '';          // Password database
$database = 'ratingums'; // Nama database
```

### 3. Menjalankan Aplikasi

1. Pastikan file-file project berada di folder htdocs (XAMPP) atau www (WAMP)
2. Akses melalui browser: `http://localhost/ratingums/`
3. Halaman utama akan menampilkan daftar tenaga kependidikan dengan rating

## Struktur Database

### Tabel `tenaga_kependidikan`
- `id` (INT, Primary Key, Auto Increment)
- `nama` (VARCHAR 255)
- `jabatan` (VARCHAR 255)
- `departemen` (VARCHAR 255)
- `email` (VARCHAR 255, Optional)
- `foto` (VARCHAR 255, Optional)
- `created_at` (TIMESTAMP)

### Tabel `ulasan`
- `id` (INT, Primary Key, Auto Increment)
- `tenaga_id` (INT, Foreign Key)
- `nama_reviewer` (VARCHAR 255)
- `rating` (INT, 1-5)
- `komentar` (TEXT)
- `tanggal` (TIMESTAMP)

## Penggunaan

1. **Melihat Daftar Staff**: Kunjungi halaman utama untuk melihat semua tenaga kependidikan
2. **Melihat Rating**: Setiap staff menampilkan rating rata-rata dan jumlah ulasan
3. **Melihat Ulasan**: Klik pada bagian ulasan untuk melihat komentar detail

## Pengembangan Lanjutan

Beberapa fitur yang bisa ditambahkan:
- [ ] Form untuk menambah ulasan baru
- [ ] Sistem login untuk reviewer
- [ ] Upload foto staff
- [ ] Filter dan pencarian staff
- [ ] Export data ke PDF/Excel
- [ ] Dashboard admin untuk mengelola data

## Lisensi

Proyek ini dibuat untuk keperluan edukasi dan dapat dimodifikasi sesuai kebutuhan.
Deployed by Muhammad Mus'ab.

## Kontak

Untuk pertanyaan atau dukungan, silakan hubungi tim pengembang:
- Email: mm240@ums.ac.id
- Instagram: [@msb.muss](https://instagram.com/msb.muss)
