-- Tabel master stok ATK
CREATE TABLE IF NOT EXISTS `stok_persediaan_atk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(150) NOT NULL,
  `satuan` varchar(25) NOT NULL DEFAULT 'pcs',
  `stok_masuk` int(11) NOT NULL DEFAULT 0,
  `stok_keluar` int(11) NOT NULL DEFAULT 0,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nama_barang` (`nama_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Log mutasi (masuk/keluar)
CREATE TABLE IF NOT EXISTS `mutasi_stok_atk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_stok` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `id_pengajuan` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_id_stok` (`id_stok`),
  KEY `idx_id_pengajuan` (`id_pengajuan`),
  CONSTRAINT `fk_mutasi_stok` FOREIGN KEY (`id_stok`) REFERENCES `stok_persediaan_atk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
