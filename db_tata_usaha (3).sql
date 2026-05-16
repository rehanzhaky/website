-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 07:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tata_usaha`
--

-- --------------------------------------------------------

--
-- Table structure for table `agenda_kegiatan`
--

CREATE TABLE `agenda_kegiatan` (
  `id` int(11) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `dokumentasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agenda_kegiatan`
--

INSERT INTO `agenda_kegiatan` (`id`, `nama_kegiatan`, `lokasi`, `tanggal`, `keterangan`, `dokumentasi`, `created_at`) VALUES
(2, 'dinas luar', 'batam', '2026-03-03', 'dinas di batam', '1778593408_ttd.jpg', '2026-05-12 13:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `data_realisasi`
--

CREATE TABLE `data_realisasi` (
  `id` int(11) NOT NULL,
  `tipe_tab` enum('jenis_belanja','sumber_dana') NOT NULL,
  `kode_dan_nama` varchar(255) NOT NULL,
  `pagu_pegawai` decimal(15,2) DEFAULT 0.00,
  `pagu_barang` decimal(15,2) DEFAULT 0.00,
  `pagu_modal` decimal(15,2) DEFAULT 0.00,
  `realisasi_pegawai` decimal(15,2) DEFAULT 0.00,
  `realisasi_barang` decimal(15,2) DEFAULT 0.00,
  `realisasi_modal` decimal(15,2) DEFAULT 0.00,
  `tanggal_input` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpa_anggaran`
--

CREATE TABLE `dpa_anggaran` (
  `id` int(11) NOT NULL,
  `kode_belanja` varchar(50) NOT NULL,
  `jenis_belanja` varchar(150) NOT NULL,
  `sumber_dana` varchar(50) NOT NULL,
  `pagu` decimal(15,2) DEFAULT 0.00,
  `realisasi` decimal(15,2) DEFAULT 0.00,
  `tanggal_input` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `e_performance`
--

CREATE TABLE `e_performance` (
  `id` int(11) NOT NULL,
  `seksi` varchar(100) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `judul_laporan` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `e_performance`
--

INSERT INTO `e_performance` (`id`, `seksi`, `bulan`, `tahun`, `judul_laporan`, `keterangan`, `nama_file`, `tanggal_upload`) VALUES
(1, 'Tata Usaha', 5, 2026, 'Laporan Kinerja Tata Usaha bulan Mei 2026', 'mantap', 'EPerf_Tata_Usaha_5_2026_1778338343.pdf', '2026-05-09 14:52:23'),
(2, 'Tata Usaha', 3, 2026, 'Laporan kinerja bulan maret', '', 'EPerf_Tata_Usaha_3_2026_1778338451.pdf', '2026-05-09 14:54:11'),
(3, 'TPI', 5, 2026, 'jfsadjef', 'ds,fhskdh', 'EPerf_TPI_5_2026_1778591061.pdf', '2026-05-12 13:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_pnbp`
--

CREATE TABLE `laporan_pnbp` (
  `id` int(11) NOT NULL,
  `tanggal_laporan` date DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_pnbp`
--

INSERT INTO `laporan_pnbp` (`id`, `tanggal_laporan`, `keterangan`, `created_at`) VALUES
(1, '2026-05-12', 'dana', '2026-05-12 11:56:55'),
(2, '2026-05-12', 'dab', '2026-05-12 11:57:12'),
(3, '2026-05-12', 'da', '2026-05-12 12:17:02'),
(4, '2026-05-12', 'new', '2026-05-12 14:08:19');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_pnbp_detail`
--

CREATE TABLE `laporan_pnbp_detail` (
  `id` int(11) NOT NULL,
  `id_laporan` int(11) DEFAULT NULL,
  `kode_akun` varchar(50) DEFAULT NULL,
  `nama_akun` varchar(255) DEFAULT NULL,
  `estimasi` bigint(20) DEFAULT 0,
  `realisasi` bigint(20) DEFAULT 0,
  `persentase` varchar(20) DEFAULT '0%'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_pnbp_detail`
--

INSERT INTO `laporan_pnbp_detail` (`id`, `id_laporan`, `kode_akun`, `nama_akun`, `estimasi`, `realisasi`, `persentase`) VALUES
(1, 1, '425131', 'Pendapatan Sewa Tanah, Gedung, dan Bangunan', 0, 0, '0'),
(2, 1, '425151', 'Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi', 16180000, 18805929, '-2625929'),
(3, 1, '425211', 'Pendapatan Paspor', 2649100000, 0, '2649100000'),
(4, 1, '425212', 'Pendapatan Visa', 900500000, 0, '900500000'),
(5, 1, '425213', 'Pendapatan Izin Keimigrasian dan Izin Masuk Kembali', 3209250000, 0, '3209250000'),
(6, 1, '425214', 'Pendapatan Pelayanan Keimigrasian Lainnya', 384500000, 0, '384500000'),
(7, 2, '425131', 'Pendapatan Sewa Tanah, Gedung, dan Bangunan', 0, 0, '0'),
(8, 2, '425151', 'Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi', 16180000, 18805929, '-2625929'),
(9, 2, '425211', 'Pendapatan Paspor', 2649100000, 0, '2649100000'),
(10, 2, '425212', 'Pendapatan Visa', 900500000, 0, '900500000'),
(11, 2, '425213', 'Pendapatan Izin Keimigrasian dan Izin Masuk Kembali', 3209250000, 0, '3209250000'),
(12, 2, '425214', 'Pendapatan Pelayanan Keimigrasian Lainnya', 384500000, 0, '384500000'),
(13, 3, '425131', 'Pendapatan Sewa Tanah, Gedung, dan Bangunan', 0, 0, '0'),
(14, 3, '425151', 'Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi', 16180000, 18805929, '-2625929'),
(15, 3, '425211', 'Pendapatan Paspor', 2649100000, 0, '2649100000'),
(16, 3, '425212', 'Pendapatan Visa', 900500000, 0, '900500000'),
(17, 3, '425213', 'Pendapatan Izin Keimigrasian dan Izin Masuk Kembali', 3209250000, 0, '3209250000'),
(18, 3, '425214', 'Pendapatan Pelayanan Keimigrasian Lainnya', 384500000, 0, '384500000'),
(19, 4, '425151', 'Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi', 16180000, 18805929, '-2625929'),
(20, 4, '425211', 'Pendapatan Paspor', 2649100000, 0, '2649100000'),
(21, 4, '425212', 'Pendapatan Visa', 900500000, 0, '900500000'),
(22, 4, '425213', 'Pendapatan Izin Keimigrasian dan Izin Masuk Kembali', 3209250000, 0, '3209250000'),
(23, 4, '425214', 'Pendapatan Pelayanan Keimigrasian Lainnya', 384500000, 0, '384500000');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_realisasi`
--

CREATE TABLE `laporan_realisasi` (
  `id` int(11) NOT NULL,
  `tanggal_laporan` date NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_sampai` date NOT NULL,
  `tipe_laporan` enum('jenis_belanja','sumber_dana') NOT NULL,
  `seksi` varchar(50) DEFAULT 'Semua',
  `keterangan` varchar(255) NOT NULL,
  `pagu_pegawai` bigint(20) DEFAULT 0,
  `realisasi_pegawai` bigint(20) DEFAULT 0,
  `sisa_pegawai` bigint(20) DEFAULT 0,
  `pagu_barang` bigint(20) DEFAULT 0,
  `realisasi_barang` bigint(20) DEFAULT 0,
  `sisa_barang` bigint(20) DEFAULT 0,
  `pagu_modal` bigint(20) DEFAULT 0,
  `realisasi_modal` bigint(20) DEFAULT 0,
  `sisa_modal` bigint(20) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rm_pagu_pegawai` bigint(20) DEFAULT 0,
  `rm_realisasi_pegawai` bigint(20) DEFAULT 0,
  `rm_sisa_pegawai` bigint(20) DEFAULT 0,
  `rm_pagu_barang` bigint(20) DEFAULT 0,
  `rm_realisasi_barang` bigint(20) DEFAULT 0,
  `rm_sisa_barang` bigint(20) DEFAULT 0,
  `rm_pagu_modal` bigint(20) DEFAULT 0,
  `rm_realisasi_modal` bigint(20) DEFAULT 0,
  `rm_sisa_modal` bigint(20) DEFAULT 0,
  `pnbp_pagu_pegawai` bigint(20) DEFAULT 0,
  `pnbp_realisasi_pegawai` bigint(20) DEFAULT 0,
  `pnbp_sisa_pegawai` bigint(20) DEFAULT 0,
  `pnbp_pagu_barang` bigint(20) DEFAULT 0,
  `pnbp_realisasi_barang` bigint(20) DEFAULT 0,
  `pnbp_sisa_barang` bigint(20) DEFAULT 0,
  `pnbp_pagu_modal` bigint(20) DEFAULT 0,
  `pnbp_realisasi_modal` bigint(20) DEFAULT 0,
  `pnbp_sisa_modal` bigint(20) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_realisasi`
--

INSERT INTO `laporan_realisasi` (`id`, `tanggal_laporan`, `periode_mulai`, `periode_sampai`, `tipe_laporan`, `seksi`, `keterangan`, `pagu_pegawai`, `realisasi_pegawai`, `sisa_pegawai`, `pagu_barang`, `realisasi_barang`, `sisa_barang`, `pagu_modal`, `realisasi_modal`, `sisa_modal`, `created_at`, `rm_pagu_pegawai`, `rm_realisasi_pegawai`, `rm_sisa_pegawai`, `rm_pagu_barang`, `rm_realisasi_barang`, `rm_sisa_barang`, `rm_pagu_modal`, `rm_realisasi_modal`, `rm_sisa_modal`, `pnbp_pagu_pegawai`, `pnbp_realisasi_pegawai`, `pnbp_sisa_pegawai`, `pnbp_pagu_barang`, `pnbp_realisasi_barang`, `pnbp_sisa_barang`, `pnbp_pagu_modal`, `pnbp_realisasi_modal`, `pnbp_sisa_modal`) VALUES
(1, '2026-04-28', '2026-04-01', '2026-04-30', 'jenis_belanja', 'Tata Usaha', 'Laporan Realisasi Anggaran Satker Bulan April 2026', 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-27 17:14:16', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, '2026-04-29', '2026-04-01', '2026-04-30', 'jenis_belanja', 'TIKKIM', 'Laporan Realisasi Anggaran TIKKIM April', 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-27 18:24:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, '2026-04-30', '2026-04-01', '2026-04-30', 'sumber_dana', 'Lantaskim', 'Laporan Realisasi Anggaran Lantaskim April', 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-04-27 18:24:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, '2026-05-10', '0000-00-00', '0000-00-00', 'sumber_dana', 'Semua', 'dana', 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-05-10 14:01:57', 5267587000, 5256411955, 11175045, 7701642000, 5526881570, 2174760430, 4301368000, 4203667850, 97700150, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(5, '2026-05-10', '0000-00-00', '0000-00-00', 'jenis_belanja', 'Semua', 'dana', 5267587000, 5256411955, 11175045, 7701642000, 5526881570, 2174760430, 4301368000, 4203667850, 97700150, '2026-05-10 14:11:11', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(6, '2026-05-11', '0000-00-00', '0000-00-00', 'jenis_belanja', 'Semua', 'gaji', 5267587000, 5256411955, 11175045, 7701642000, 5526881570, 2174760430, 4301368000, 4203667850, 97700150, '2026-05-11 12:46:35', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_realisasi_bidang`
--

CREATE TABLE `laporan_realisasi_bidang` (
  `id` int(11) NOT NULL,
  `tanggal_laporan` date DEFAULT NULL,
  `seksi` varchar(100) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_realisasi_bidang`
--

INSERT INTO `laporan_realisasi_bidang` (`id`, `tanggal_laporan`, `seksi`, `keterangan`, `created_at`) VALUES
(1, '2026-05-12', 'PEMERIKSAAN KEIMIGRASIAN (TPI)', 'mei', '2026-05-12 04:35:59'),
(2, '2026-05-12', 'PEMERIKSAAN KEIMIGRASIAN (TPI)', 'Bulan April', '2026-05-12 04:58:59'),
(3, '2026-05-12', 'INTELDAKIM', 'Mei', '2026-05-12 05:26:00'),
(4, '2026-05-12', 'URUSAN KEUANGAN', 'bulan mei', '2026-05-12 13:20:51'),
(5, '2026-05-12', 'LANTASKIM', 'mei', '2026-05-12 14:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_realisasi_bidang_detail`
--

CREATE TABLE `laporan_realisasi_bidang_detail` (
  `id` int(11) NOT NULL,
  `id_laporan` int(11) DEFAULT NULL,
  `kode_komponen` varchar(50) DEFAULT NULL,
  `judul_komponen` varchar(255) DEFAULT NULL,
  `pagu` bigint(20) DEFAULT 0,
  `realisasi` bigint(20) DEFAULT 0,
  `sisa` bigint(20) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_realisasi_bidang_detail`
--

INSERT INTO `laporan_realisasi_bidang_detail` (`id`, `id_laporan`, `kode_komponen`, `judul_komponen`, `pagu`, `realisasi`, `sisa`) VALUES
(1, 1, 'QIB 002', 'Pemeriksaan Keimigrasian di TPI', 596904000, 116622210, 480281790),
(2, 1, 'BIF U11', 'Pemeriksaan Keimigrasian Non Reguler Wilayah Barat', 143696000, 23330000, 120366000),
(3, 2, 'QIB 002', 'Pemeriksaan Keimigrasian di TPI', 596904000, 116622210, 480281790),
(4, 2, 'BIF U11', 'Pemeriksaan Keimigrasian Non Reguler Wilayah Barat', 143696000, 23330000, 120366000),
(5, 3, 'BHB U11', 'Operasi intelejen Keimigrasian di Wilayah Barat', 29790000, 98, 29789902),
(6, 3, 'BHB U14', 'Operasi Gabungan di Wilayah Barat', 11674000, 22, 11673978),
(7, 3, 'BHB U17', 'Penyidikan TIndak Pidana Keimigrasian di Wilayah Barat', 0, 0, 0),
(8, 3, 'BIB 001', 'Penyidikan TIndakan Administratif Keimigrasian', 20885200, 60, 20885140),
(9, 3, 'BKA 002', 'Pembentukan dan Pembinaan Desa Binaan Imigrasi', 0, 0, 0),
(10, 3, 'QHB U02', 'Operasi di Wilayah Mandiri', 96600000, 61, 96599939),
(11, 4, 'EBD 001', 'Koordinasi dan Konsultasi Perencanaan dan Pelaksanaan Anggaran', 52303060, 26, 52303034),
(12, 4, 'EBD Z27', 'Layanan Manajemen Keuangan', 0, 0, 0),
(13, 5, 'BAA 001', 'Layanan Penerbitan Dokumen Perjalanan RI', 62909740, 3, 62909737);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_realisasi_detail`
--

CREATE TABLE `laporan_realisasi_detail` (
  `id` int(11) NOT NULL,
  `id_laporan` int(11) NOT NULL,
  `kode_anggaran` varchar(50) NOT NULL,
  `nama_anggaran` varchar(150) NOT NULL,
  `pagu` decimal(15,2) DEFAULT 0.00,
  `realisasi_pegawai` decimal(15,2) DEFAULT 0.00,
  `realisasi_barang` decimal(15,2) DEFAULT 0.00,
  `realisasi_modal` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_umum`
--

CREATE TABLE `laporan_umum` (
  `id` int(11) NOT NULL,
  `seksi` varchar(50) NOT NULL,
  `bulan` int(11) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `judul_laporan` varchar(255) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tanggal_upload` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_umum`
--

INSERT INTO `laporan_umum` (`id`, `seksi`, `bulan`, `tahun`, `judul_laporan`, `nama_file`, `tanggal_upload`, `keterangan`, `created_at`) VALUES
(1, 'Tata Usaha', 4, 2026, 'mvmhnvj', '1777390751_Form_Pengajuan_5.pdf', '2026-04-28', 'mhbmjbjh', '2026-04-28 15:39:11'),
(2, 'Tata Usaha', 5, 2026, 'mhthskfshg', '1777897625_Laporan_Realisasi_20260427_195502.xls', '2026-05-04', ',jnrtrlmhfljf', '2026-05-04 12:27:05'),
(3, 'Tata Usaha', 5, 2026, 'rsrrgsfdgg', '1777898346_logo_header_2025.webp', '2026-05-04', 'krkrkrkekrka', '2026-05-04 12:39:06'),
(4, 'Tata Usaha', 5, 2026, 'mhthskfshg', '1778550008_Realisasi_Belanja_Kantor_Imigrasi.pdf', '2026-05-12', 'tes\r\n', '2026-05-12 01:40:08'),
(5, 'TPI', 5, 2026, 'laporan', '1778589863_Laporan_Fa_Detail_(16_Segmen)_-_2026-05-11T115117.005.pdf', '2026-05-12', '', '2026-05-12 12:44:23'),
(6, 'Tata Usaha', 5, 2026, 'umum', '1778593588_Realisasi_Pendapatan_Per_Akun_(18).pdf', '2026-05-12', 'laporan bmn', '2026-05-12 13:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_inventaris`
--

CREATE TABLE `pengajuan_inventaris` (
  `id` int(11) NOT NULL,
  `seksi` varchar(100) NOT NULL,
  `nama_surat` varchar(150) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_pengaju` varchar(255) NOT NULL,
  `diketahui_oleh` varchar(100) DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan_inventaris`
--

INSERT INTO `pengajuan_inventaris` (`id`, `seksi`, `nama_surat`, `status`, `tanggal`, `nama_pengaju`, `diketahui_oleh`, `lampiran`) VALUES
(11, 'Tikkim', 'gokil mana gokil', 'reject', '2026-05-13 17:00:00', 'povinme', 'Drs. Ahmad Fauzi, M.Si', '1777053366_69ebaeb6c9787.jpg'),
(15, 'Tata usaha', '', 'approve', '2026-02-02 17:00:00', '', 'Drs. Ahmad Fauzi, M.Si', '1778591493_6a0327055b7b6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_inventaris_detail`
--

CREATE TABLE `pengajuan_inventaris_detail` (
  `id` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(25) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan_inventaris_detail`
--

INSERT INTO `pengajuan_inventaris_detail` (`id`, `id_pengajuan`, `nama_barang`, `jumlah`, `satuan`, `keterangan`) VALUES
(4, 11, 'njh,', 2, 'sila', 'kebutuhan perut'),
(13, 15, 'pena', 1, 'pak', 'tes');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_keuangan`
--

CREATE TABLE `pengaturan_keuangan` (
  `id` int(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan_keuangan`
--

INSERT INTO `pengaturan_keuangan` (`id`, `password`) VALUES
(1, '$2y$10$VvIsP7LIU4Pkbbs0P76GJ.37Yc/5LbmvRDBlw.TPDfQYFi7LEJRVy');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `seksi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `seksi`, `created_at`) VALUES
(1, 'Admin ', '$2y$10$cYXHXDnhfonYLymbJXKZIeHWuyppKNU9xTHMm0Tc2O1qcSpdEfamG', 'Admin utama', 'admin_utama', 'Semua Seksi', '2026-04-28 17:13:20'),
(4, 'normalisasi', '$2y$10$YDzMSfZgXeBuqEys6g0LweLzbVPxPU8HUXvigYAb2gXVGKvaMF.l2', 'nur malisasi', 'user', 'TIKKIM', '2026-05-05 11:47:17'),
(5, 'jawir', '$2y$10$RT1ggEBThvG3ZUVPPY971OEHamsYqTCmU1IEnSG3y4Z0BJBOtFG.S', 'sejawir', 'user', 'INTELDAKIM', '2026-05-05 11:48:57'),
(7, 'budi123', '$2y$10$CDJJanSxH3cyz/AoAeoG0Oxi1oaj4sMS6N836z28ROQaWXCwo4hve', 'budi', 'user', 'TPI', '2026-05-11 13:28:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agenda_kegiatan`
--
ALTER TABLE `agenda_kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_realisasi`
--
ALTER TABLE `data_realisasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dpa_anggaran`
--
ALTER TABLE `dpa_anggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `e_performance`
--
ALTER TABLE `e_performance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_pnbp`
--
ALTER TABLE `laporan_pnbp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_pnbp_detail`
--
ALTER TABLE `laporan_pnbp_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_laporan` (`id_laporan`);

--
-- Indexes for table `laporan_realisasi`
--
ALTER TABLE `laporan_realisasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_realisasi_bidang`
--
ALTER TABLE `laporan_realisasi_bidang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_realisasi_bidang_detail`
--
ALTER TABLE `laporan_realisasi_bidang_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_laporan` (`id_laporan`);

--
-- Indexes for table `laporan_realisasi_detail`
--
ALTER TABLE `laporan_realisasi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_laporan` (`id_laporan`);

--
-- Indexes for table `laporan_umum`
--
ALTER TABLE `laporan_umum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_inventaris`
--
ALTER TABLE `pengajuan_inventaris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_inventaris_detail`
--
ALTER TABLE `pengajuan_inventaris_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `pengaturan_keuangan`
--
ALTER TABLE `pengaturan_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agenda_kegiatan`
--
ALTER TABLE `agenda_kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_realisasi`
--
ALTER TABLE `data_realisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dpa_anggaran`
--
ALTER TABLE `dpa_anggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_performance`
--
ALTER TABLE `e_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laporan_pnbp`
--
ALTER TABLE `laporan_pnbp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_pnbp_detail`
--
ALTER TABLE `laporan_pnbp_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `laporan_realisasi`
--
ALTER TABLE `laporan_realisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `laporan_realisasi_bidang`
--
ALTER TABLE `laporan_realisasi_bidang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_realisasi_bidang_detail`
--
ALTER TABLE `laporan_realisasi_bidang_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `laporan_realisasi_detail`
--
ALTER TABLE `laporan_realisasi_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_umum`
--
ALTER TABLE `laporan_umum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengajuan_inventaris`
--
ALTER TABLE `pengajuan_inventaris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pengajuan_inventaris_detail`
--
ALTER TABLE `pengajuan_inventaris_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laporan_pnbp_detail`
--
ALTER TABLE `laporan_pnbp_detail`
  ADD CONSTRAINT `laporan_pnbp_detail_ibfk_1` FOREIGN KEY (`id_laporan`) REFERENCES `laporan_pnbp` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_realisasi_bidang_detail`
--
ALTER TABLE `laporan_realisasi_bidang_detail`
  ADD CONSTRAINT `laporan_realisasi_bidang_detail_ibfk_1` FOREIGN KEY (`id_laporan`) REFERENCES `laporan_realisasi_bidang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_realisasi_detail`
--
ALTER TABLE `laporan_realisasi_detail`
  ADD CONSTRAINT `laporan_realisasi_detail_ibfk_1` FOREIGN KEY (`id_laporan`) REFERENCES `laporan_realisasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_inventaris_detail`
--
ALTER TABLE `pengajuan_inventaris_detail`
  ADD CONSTRAINT `fk_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_inventaris` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
