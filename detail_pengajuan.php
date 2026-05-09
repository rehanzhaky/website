<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

// Tangkap ID Pengajuan
$id = $_GET['id'] ?? 0;

// Tarik Data Master
$stmt = $pdo->prepare("SELECT * FROM pengajuan_inventaris WHERE id = ?");
$stmt->execute([$id]);
$pengajuan = $stmt->fetch();

if (!$pengajuan) {
    die("<script>alert('Data tidak ditemukan!'); window.location.href='daftar_pengajuan.php';</script>");
}

// Cek Status: PENDING vs SUDAH DIPROSES
$status_db = strtolower(trim($pengajuan['status']));
$is_approved = in_array($status_db, ['approve', 'approved', 'diterima', 'terima', 'acc', 'disetujui', 'selesai']);
$is_rejected = in_array($status_db, ['reject', 'rejected', 'ditolak', 'tolak']);

// Kalau bukan Approved & bukan Rejected, berarti masih PENDING (Bisa di-Edit)
$is_pending = (!$is_approved && !$is_rejected);

// ==========================================
// PROSES UPDATE DATA (HANYA JIKA PENDING)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $is_pending) {
    $seksi = $_POST['seksi'];
    $tanggal = $_POST['tanggal'];
    $diketahui_oleh = $_POST['diketahui_oleh'];

    try {
        $pdo->beginTransaction();

        // 1. Update Tabel Master
        $sql_update = "UPDATE pengajuan_inventaris SET seksi = ?, tanggal = ?, diketahui_oleh = ? WHERE id = ?";
        $pdo->prepare($sql_update)->execute([$seksi, $tanggal, $diketahui_oleh, $id]);

        // 2. Bersihkan detail lama (Delete)
        $pdo->prepare("DELETE FROM pengajuan_inventaris_detail WHERE id_pengajuan = ?")->execute([$id]);

        // 3. Masukkan detail yang baru diedit (Insert ulang)
        if (isset($_POST['nama_barang']) && is_array($_POST['nama_barang'])) {
            $sql_detail = "INSERT INTO pengajuan_inventaris_detail (id_pengajuan, nama_barang, jumlah, satuan, keterangan) VALUES (?, ?, ?, ?, ?)";
            $stmt_detail = $pdo->prepare($sql_detail);

            foreach ($_POST['nama_barang'] as $key => $val) {
                if (!empty(trim($val))) {
                    $stmt_detail->execute([
                        $id,
                        $val,
                        $_POST['jumlah'][$key],
                        $_POST['satuan'][$key],
                        $_POST['keterangan'][$key]
                    ]);
                }
            }
        }

        $pdo->commit();
        $sukses = "Data pengajuan berhasil diperbarui! ✅";
        
        // Tarik data ulang biar halamannya nampilin data terbaru
        $stmt->execute([$id]);
        $pengajuan = $stmt->fetch();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Gagal menyimpan perubahan: " . $e->getMessage();
    }
}

// Tarik Data Detail Barang (Buat ditampilin di form)
$stmt_detail = $pdo->prepare("SELECT * FROM pengajuan_inventaris_detail WHERE id_pengajuan = ?");
$stmt_detail->execute([$id]);
$details = $stmt_detail->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Detail Pengajuan Inventaris</h2>
        <p>Rincian data permohonan barang masuk.</p>
    </div>
    <a href="daftar_pengajuan.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama">

    <?php if ($is_approved): ?>
        <div style="background: rgba(80, 250, 123, 0.15); border: 1px solid #50fa7b; color: #50fa7b; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center;">
            <h3 style="margin: 0 0 5px 0;">✅ PENGAJUAN TELAH DISETUJUI</h3>
            <p style="margin: 0; font-size: 13px; opacity: 0.9;">Dokumen ini telah dikunci dan tidak dapat diubah lagi. Silakan cetak PDF untuk pengarsipan.</p>
        </div>
    <?php elseif ($is_rejected): ?>
        <div style="background: rgba(255, 85, 85, 0.15); border: 1px solid #ff5555; color: #ff5555; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center;">
            <h3 style="margin: 0 0 5px 0;">❌ PENGAJUAN DITOLAK</h3>
            <p style="margin: 0; font-size: 13px; opacity: 0.9;">Permohonan ini tidak disetujui oleh pimpinan dan dokumen telah dikunci.</p>
        </div>
    <?php else: ?>
        <div style="background: rgba(255, 184, 108, 0.15); border: 1px solid #ffb86c; color: #ffb86c; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center;">
            <h3 style="margin: 0 0 5px 0;">⏳ STATUS: MENUNGGU (PENDING)</h3>
            <p style="margin: 0; font-size: 13px; opacity: 0.9;">Anda masih dapat mengedit rincian barang sebelum pengajuan ini diproses oleh pimpinan.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($sukses)): ?>
        <div style="background: rgba(80, 250, 123, 0.1); border: 1px solid rgba(80, 250, 123, 0.3); color: #50fa7b; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            <?= $sukses ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 85, 85, 0.1); border: 1px solid rgba(255, 85, 85, 0.3); color: #ff5555; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Informasi Surat</h3>
        
        <div class="form-grid">
            <div class="form-group form-full" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Seksi / Subseksi</label>
                    <input type="text" name="seksi" value="<?= htmlspecialchars($pengajuan['seksi']) ?>" required <?= !$is_pending ? 'readonly style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
                </div>
                <div style="flex: 1;">
                    <label>Tanggal Pengajuan</label>
                    <input type="date" name="tanggal" value="<?= htmlspecialchars($pengajuan['tanggal']) ?>" required <?= !$is_pending ? 'readonly style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
                </div>
            </div>

            <div class="form-group form-full">
                <label>Diketahui Oleh (Pimpinan)</label>
                <input type="text" name="diketahui_oleh" value="<?= htmlspecialchars($pengajuan['diketahui_oleh']) ?>" required <?= !$is_pending ? 'readonly style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
            </div>
        </div>

        <h3 style="margin-top: 25px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Daftar Barang</h3>

        <div id="container-barang">
            <?php foreach ($details as $row): ?>
            <div class="form-grid row-barang" style="align-items: end; background: rgba(0,0,0,0.1); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang[]" value="<?= htmlspecialchars($row['nama_barang']) ?>" required <?= !$is_pending ? 'readonly style="opacity:0.6;"' : '' ?>>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah[]" value="<?= htmlspecialchars($row['jumlah']) ?>" required <?= !$is_pending ? 'readonly style="opacity:0.6;"' : '' ?>>
                </div>
                <div class="form-group form-full" style="margin-bottom: 0;">
                    <label>Satuan</label>
                    <input type="text" name="satuan[]" value="<?= htmlspecialchars($row['satuan']) ?>" required <?= !$is_pending ? 'readonly style="opacity:0.6;"' : '' ?>>
                </div>
                <div class="form-group form-full" style="margin-bottom: 0;">
                    <label>Keterangan / Alasan</label>
                    <input type="text" name="keterangan[]" value="<?= htmlspecialchars($row['keterangan']) ?>" <?= !$is_pending ? 'readonly style="opacity:0.6;"' : '' ?>>
                </div>
                
                <?php if ($is_pending): ?>
                <div class="form-group" style="margin-bottom: 0; text-align: center;">
                    <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="background: rgba(255, 76, 76, 0.2); color: #ff4c4c; border: 1px solid #ff4c4c; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%;">Hapus</button>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($is_pending): ?>
            <button type="button" onclick="tambahBaris()" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.3); color: white; border-radius: 8px; cursor: pointer; margin-top: 10px; transition: all 0.3s;">
                + Tambah Baris Barang Lain
            </button>
        <?php endif; ?>

        <div style="display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 30px;">
            <?php if ($is_approved): ?>
                <a href="cetak.php?id=<?= $pengajuan['id'] ?>" target="_blank" class="btn-shortcut" style="background: #4facfe; border-color: #4facfe; text-decoration: none; width: auto; padding: 12px 25px;">
                    🖨️ Cetak Formulir PDF
                </a>
            <?php endif; ?>

            <?php if ($is_pending): ?>
                <button type="submit" class="btn-shortcut" style="width: 250px;">💾 Simpan Perubahan</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if ($is_pending): ?>
<script>
    // JS buat nambah/hapus baris barang cuma aktif pas mode PENDING
    function tambahBaris() {
        const container = document.getElementById('container-barang');
        const barisBaru = document.createElement('div');
        barisBaru.className = 'form-grid row-barang';
        barisBaru.style.cssText = 'align-items: end; background: rgba(0,0,0,0.1); padding: 15px; border-radius: 10px; margin-bottom: 10px;';
        
        barisBaru.innerHTML = `
            <div class="form-group" style="margin-bottom: 0;">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang[]" required autocomplete="off">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Jumlah</label>
                <input type="number" name="jumlah[]" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Satuan</label>
                <input type="text" name="satuan[]" required autocomplete="off">
            </div>
            <div class="form-group form-full" style="margin-bottom: 0;">
                <label>Keterangan</label>
                <input type="text" name="keterangan[]" autocomplete="off">
            </div>
            <div class="form-group" style="margin-bottom: 0; text-align: center;">
                <button type="button" onclick="hapusBaris(this)" style="background: rgba(255, 76, 76, 0.2); color: #ff4c4c; border: 1px solid #ff4c4c; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%;">Hapus</button>
            </div>
        `;
        container.appendChild(barisBaru);
    }

    function hapusBaris(button) {
        const baris = button.closest('.row-barang');
        const jumlahBaris = document.querySelectorAll('.row-barang').length;
        if (jumlahBaris > 1) {
            baris.remove();
        } else {
            alert('Minimal harus ada 1 barang bos!');
        }
    }
</script>
<?php endif; ?>

<?php include 'layouts/footer.php'; ?>