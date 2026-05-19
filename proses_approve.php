<?php
session_start();
require_once 'config/koneksi.php';

$role = strtolower($_SESSION['role'] ?? '');
$akses_admin = ['admin_utama', 'tu_kepegawaian', 'tu_keuangan', 'tu_umum']; 

if (!isset($_SESSION['user_id']) || !in_array($role, $akses_admin)) {
    echo "<script>alert('Waduh! Anda tidak memiliki hak akses.'); window.location.href='daftar_pengajuan.php';</script>";
    exit;
}

$id = $_GET['id'] ?? 0;
$aksi = strtolower($_GET['aksi'] ?? '');

if ($aksi === 'terima') {
    $status_baru = 'approve';
    $pesan = 'Mantap! Pengajuan berhasil di-ACC. ✅';
} elseif ($aksi === 'tolak') {
    $status_baru = 'reject';
    $pesan = 'Pengajuan resmi DITOLAK. ❌';
} else {
    echo "<script>alert('Aksi tidak dikenali sistem!'); window.location.href='daftar_pengajuan.php';</script>";
    exit;
}

try {
    $cek = $pdo->prepare("SELECT status FROM pengajuan_inventaris WHERE id = ?");
    $cek->execute([$id]);
    $status_lama = strtolower(trim($cek->fetchColumn() ?: ''));
    $sudah_approved = in_array($status_lama, ['approve', 'approved', 'diterima', 'terima', 'acc', 'disetujui', 'selesai']);

    $pdo->beginTransaction();

    $sql = "UPDATE pengajuan_inventaris SET status = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$status_baru, $id]);

    $item_kurang_stok = [];
    $item_tidak_terdaftar = [];

    if ($status_baru === 'approve' && !$sudah_approved) {
        $det = $pdo->prepare("SELECT nama_barang, jumlah FROM pengajuan_inventaris_detail WHERE id_pengajuan = ?");
        $det->execute([$id]);
        $items = $det->fetchAll();

        $find = $pdo->prepare("SELECT id, jumlah FROM stok_persediaan_atk WHERE LOWER(nama_barang) = LOWER(?) LIMIT 1");
        $upd  = $pdo->prepare("UPDATE stok_persediaan_atk SET stok_keluar = stok_keluar + ?, jumlah = jumlah - ? WHERE id = ?");
        $log  = $pdo->prepare("INSERT INTO mutasi_stok_atk (id_stok, jenis, jumlah, keterangan, id_pengajuan, id_user) VALUES (?, 'keluar', ?, ?, ?, ?)");

        foreach ($items as $it) {
            $nama = trim($it['nama_barang']);
            $qty  = (int)$it['jumlah'];
            if ($nama === '' || $qty <= 0) continue;

            $find->execute([$nama]);
            $stok = $find->fetch();
            if (!$stok) {
                $item_tidak_terdaftar[] = $nama;
                continue;
            }
            if ((int)$stok['jumlah'] < $qty) {
                $item_kurang_stok[] = $nama . " (butuh $qty, tersedia " . (int)$stok['jumlah'] . ")";
                continue;
            }

            $upd->execute([$qty, $qty, $stok['id']]);
            $log->execute([$stok['id'], $qty, "Pengeluaran via pengajuan #$id", $id, $_SESSION['user_id']]);
        }
    }

    $pdo->commit();

    $info = '';
    if (!empty($item_tidak_terdaftar)) {
        $info .= '\\n\\n⚠️ Item berikut TIDAK terdaftar di Stok Persediaan ATK (stok tidak terpotong): ' . implode(', ', $item_tidak_terdaftar);
    }
    if (!empty($item_kurang_stok)) {
        $info .= '\\n\\n⚠️ Stok TIDAK MENCUKUPI untuk: ' . implode(', ', $item_kurang_stok);
    }

    echo "<script>alert('" . addslashes($pesan) . $info . "'); window.location.href='daftar_pengajuan.php';</script>";
} catch (Exception $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    die("Waduh, Error Database Bos: " . $e->getMessage());
}
?>