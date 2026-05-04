# 🎓 RatingUMS — Sistem Rating Tenaga Kependidikan FKIP UMS

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white"/>
  <img src="https://img.shields.io/badge/License-Educational-green?style=for-the-badge"/>
</p>

> Sistem web untuk menampilkan dan mengelola rating tenaga kependidikan Fakultas Keguruan dan Ilmu Pendidikan (FKIP) Universitas Muhammadiyah Surakarta (UMS). Mahasiswa dan civitas akademika dapat memberikan ulasan berbintang secara langsung melalui website.

---

## 📸 Preview

| Halaman Utama | Detail Ulasan | Panel Admin |
|:---:|:---:|:---:|
| Daftar tenaga kependidikan dengan rating | Ulasan & komentar per staff | Kelola data, foto, dan ulasan |

---

## ✨ Fitur Utama

- 📋 **Daftar Tenaga Kependidikan** — Menampilkan seluruh tenaga kependidikan FKIP UMS beserta jabatan, NIK, email, dan foto
- ⭐ **Sistem Rating Bintang** — Rating 1–5 bintang dengan rata-rata otomatis per staff
- 💬 **Ulasan & Komentar** — Mahasiswa dapat memberikan ulasan dan komentar dengan nama reviewer
- 🚨 **Laporan SDM Rendah** — Ulasan dengan rating rendah dikelompokkan secara terpisah untuk evaluasi
- 🔐 **Panel Admin** — Pengelolaan data tenaga, upload/ganti foto, dan moderasi ulasan
- 🖱️ **Drag-and-Drop Urutan** — Admin bisa mengatur urutan tampilan staff dengan cara drag & drop (SortableJS)
- 📤 **Export CSV** — Export data ulasan ke format CSV
- 📱 **Responsif** — Tampilan mobile-friendly untuk semua ukuran layar
- 🔔 **Berita & Pengumuman** — Halaman informasi terkait rating FKIP

---

## 🗂️ Struktur File

```
ratingums/
├── index.php                     # Halaman utama — daftar tenaga kependidikan
├── ulasanratingtenaga.php        # Halaman ulasan per tenaga (submit review)
├── ulasan.php                    # Daftar semua ulasan
├── sdmrendah.php                 # Ulasan SDM rendah (rating rendah)
├── tberitarating.php             # Halaman berita/pengumuman rating
├── daftar-tenaga-kependidikan.php# Halaman daftar lengkap tenaga
├── dashboard.php                 # Dashboard statistik admin
├── panel.php                     # Panel admin kelola data
├── login.php                     # Halaman login admin
├── auth_check.php                # Middleware cek sesi login
├── export_csv.php                # Export data ke CSV
├── update_urutan.php             # AJAX endpoint untuk update urutan drag-drop
├── db_alter.php                  # Utility alter struktur database
├── config.php                    # Konfigurasi koneksi database ⚠️
├── footer.php                    # Komponen footer yang dibagikan
├── style.css                     # Stylesheet utama
├── database.sql                  # Skema database & data awal
└── img/                          # Direktori foto tenaga kependidikan
```

---

## 🗄️ Struktur Database

### Tabel `tenaga_kependidikan`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (PK, AI) | ID unik |
| `nama` | VARCHAR(255) | Nama lengkap |
| `jabatan` | VARCHAR(255) | Jabatan/posisi |
| `nik` | VARCHAR(4) | Nomor Induk Kepegawaian (4 digit) |
| `email` | VARCHAR(255) | Email institusional |
| `foto` | VARCHAR(255) | Path foto profil |
| `urutan` | INT | Urutan tampilan (drag-and-drop) |
| `created_at` | TIMESTAMP | Waktu ditambahkan |

### Tabel `ulasan`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (PK, AI) | ID ulasan |
| `tenaga_id` | INT (FK) | Referensi ke tenaga kependidikan |
| `nama_reviewer` | VARCHAR(255) | Nama pemberi ulasan |
| `rating` | INT (1–5) | Rating bintang |
| `komentar` | TEXT | Isi komentar |
| `tanggal` | TIMESTAMP | Waktu ulasan dikirim |

### Tabel `ulasan_sdm_rendah`
> Struktur identik dengan tabel `ulasan`. Menyimpan ulasan yang dikategorikan SDM rendah secara terpisah untuk evaluasi internal.

---

## ⚙️ Persyaratan Sistem

- PHP **7.4** atau lebih tinggi (direkomendasikan PHP 8.x)
- MySQL **5.7+** / MariaDB **10.4+**
- Web server: **Apache** atau **Nginx** (XAMPP / WAMP / Laragon)
- Browser modern (Chrome, Firefox, Edge)

---

## 🚀 Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/kopitubruk5k/ratingums.git
cd ratingums
```

### 2. Setup Database

1. Buka **phpMyAdmin** atau MySQL client
2. Buat database baru:
   ```sql
   CREATE DATABASE ratingums CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```
3. Import skema dan data awal:
   ```bash
   mysql -u root -p ratingums < database.sql
   ```

### 3. Konfigurasi Database

Edit file `config.php`:

```php
$host     = 'localhost';   // Host database
$user     = 'root';        // Username database
$password = '';            // Password database
$database = 'ratingums';  // Nama database
```

> ⚠️ **Jangan pernah commit `config.php` ke repository publik** jika sudah berisi kredensial produksi!

### 4. Jalankan Aplikasi

1. Salin folder project ke direktori web server:
   - **XAMPP**: `C:/xampp/htdocs/ratingums/`
   - **Laragon**: `C:/laragon/www/ratingums/`
2. Pastikan folder `img/` memiliki permission write untuk upload foto
3. Akses melalui browser: `http://localhost/ratingums/`

---

## 🔑 Login Admin

Akses halaman admin melalui: `http://localhost/ratingums/login.php`

> Kredensial default dikonfigurasi langsung di `config.php` atau database. Pastikan menggantinya di lingkungan produksi.

---

## 🌐 Deploy ke Hosting (Hostinger)

1. Upload semua file ke folder subdomain via **File Manager** atau **FTP**
2. Buat database baru di **hPanel** → MySQL Databases
3. Import `database.sql` via phpMyAdmin
4. Update `config.php` dengan kredensial database produksi
5. Pastikan folder `img/` memiliki permission **755**

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP (Native) |
| Database | MySQL / MariaDB |
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Font | Google Fonts — Poppins |
| Drag & Drop | [SortableJS](https://sortablejs.github.io/Sortable/) |

---

## 🤝 Kontribusi

Pull request sangat disambut! Untuk perubahan besar, silakan buka issue terlebih dahulu untuk mendiskusikan apa yang ingin diubah.

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan edukasi dan pengembangan institusi.  
© 2026 — Developed by **Muhammad Mus'ab** | FKIP UMS

---

## 📬 Kontak

- 📧 Email: [mm240@ums.ac.id](mailto:mm240@ums.ac.id)
- 📸 Instagram: [@msb.muss](https://instagram.com/msb.muss)
- 🌐 Live Demo: [ratingtendik-fkipums.sdumsgc.com](https://ratingtendik-fkipums.sdumsgc.com)
