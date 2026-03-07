-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Jan 2026 pada 05.42
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ratingums`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tenaga_kependidikan`
--

CREATE TABLE `tenaga_kependidikan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `nik` varchar(4) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tenaga_kependidikan`
--

INSERT INTO `tenaga_kependidikan` (`id`, `nama`, `jabatan`, `nik`, `email`, `foto`, `created_at`) VALUES
(1, 'Murdayanto, S.Kom', 'Kasubag Tata Usaha', '1133', 'murdayanto@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(2, 'Baroroh Rina T., S.Psi.', 'Kaur Keuangan & Akuntansi', '1138', 'brt223@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(3, 'Pudjianto, S.Pd.', 'Kaur Akademik', '1153', 'pud101@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(4, 'Mulyanto', 'Kaur Umum & Sarpras', '1272', 'mul298@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(5, 'Djoko Sardjono, S.E.', 'Staf Keuangan', '1834', 'ds738@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(6, 'Muhammad Syaifudin Bachtiar, S.Pd.', 'Staf Keuangan', '1979', 'msb181@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(7, 'Nur Annisa Novia Fauziah S.Ak', 'Staf Keuangan', '2094', 'nan284@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(8, 'M. Rahadian, S.Kom', 'Laboran Komputer', '1884', 'mr561@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(9, 'Jarot Wiryatmoko, S.Pd.', 'Staf Lab. Pembelajaran Terintegrasi', '1866', 'jw725@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(10, 'Lilis Setiawati, A.Ma.Pust.', 'Staf Lab. Pembelajaran Terintegrasi', '1844', 'ls170@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(11, 'Ainun Rahma Firdausy, S,Pd., M.Pd', 'Unit Penjaminan Mutu', '2108', 'arf549@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(12, 'Sugiman', 'Staf Tata Usaha', '939', 'sug222@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(13, 'Suharno', 'Staf Tata Usaha', '1020', 'suh255@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(14, 'Suratman', 'Staf Tata Usaha', '1023', 'sur245@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(15, 'Arifin Sri Hascaryo, S.E', 'Staf Tata Usaha', '2103', 'ash308@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(16, 'Arief Rahman Hanif, S.I.Kom', 'Staf Tata Usaha', '2106', 'arh503@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(17, 'Afif Jauhari, S.E.', 'Staf Pendidikan Profesi Guru', '1889', 'aj716@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(18, 'Zuhdi Fatkhurrahman S.Kom', 'Laboran Komputer', '2115', 'zf806@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(19, 'Hirtanto, S.Pd., M.Pd.', 'Staf Lab. Pend. Matematika', '1900', 'hir634@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(20, 'Rivky Arif Rahmat, S.Pd., M.Pd.', 'Laboran Pend. Biologi', '500.', 'rar883@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(21, 'Ayu Fatonah S.Pd', 'Laboran Kosmografi Pend. Geografi', '2121', 'af142@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(22, 'Muhammad Mus’ab, S.Pd.', 'Laboran Pend. Teknik Informatika', '2254', 'mm240@ums.ac.id', NULL, '2026-01-05 03:17:59'),
(23, 'Fathurrahman, S.Pd.', 'Laboran Pend. Olahraga', '2256', 'fat113@ums.ac.id', NULL, '2026-01-05 03:17:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `tenaga_id` int(11) NOT NULL,
  `nama_reviewer` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `komentar` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ulasan`
--

INSERT INTO `ulasan` (`id`, `tenaga_id`, `nama_reviewer`, `rating`, `komentar`, `tanggal`) VALUES
(15, 1, 'kekoc', 5, 'keren', '2026-01-05 03:30:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan_sdm_rendah`
--

CREATE TABLE `ulasan_sdm_rendah` (
  `id` int(11) NOT NULL,
  `tenaga_id` int(11) NOT NULL,
  `nama_reviewer` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `komentar` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ulasan_sdm_rendah`
--

INSERT INTO `ulasan_sdm_rendah` (`id`, `tenaga_id`, `nama_reviewer`, `rating`, `komentar`, `tanggal`) VALUES
(4, 1, 'anjing gila', 2, 'anjing gila', '2026-01-05 03:30:20');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tenaga_kependidikan`
--
ALTER TABLE `tenaga_kependidikan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenaga_id` (`tenaga_id`);

--
-- Indeks untuk tabel `ulasan_sdm_rendah`
--
ALTER TABLE `ulasan_sdm_rendah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenaga_id` (`tenaga_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tenaga_kependidikan`
--
ALTER TABLE `tenaga_kependidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `ulasan_sdm_rendah`
--
ALTER TABLE `ulasan_sdm_rendah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`tenaga_id`) REFERENCES `tenaga_kependidikan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ulasan_sdm_rendah`
--
ALTER TABLE `ulasan_sdm_rendah`
  ADD CONSTRAINT `ulasan_sdm_rendah_ibfk_1` FOREIGN KEY (`tenaga_id`) REFERENCES `tenaga_kependidikan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
