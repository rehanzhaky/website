<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
if (!in_array($role, ['admin_utama', 'tu_keuangan'])) {
    echo "<script>alert('Akses Ditolak!'); window.location.href='pilih_laporan.php';</script>";
    exit;
}

require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_laporan = $_POST['tanggal_laporan'];
    $keterangan = $_POST['keterangan'];

    try {
        $pdo->beginTransaction();

        $stmt_master = $pdo->prepare("INSERT INTO laporan_pnbp (tanggal_laporan, keterangan) VALUES (?, ?)");
        $stmt_master->execute([$tanggal_laporan, $keterangan]);
        $id_laporan = $pdo->lastInsertId();

        if (isset($_POST['kode_akun'])) {
            $stmt_detail = $pdo->prepare("INSERT INTO laporan_pnbp_detail (id_laporan, kode_akun, nama_akun, estimasi, realisasi, persentase) VALUES (?, ?, ?, ?, ?, ?)");
            
            for ($i = 0; $i < count($_POST['kode_akun']); $i++) {
                $kode = $_POST['kode_akun'][$i];
                $nama = $_POST['nama_akun'][$i];
                $estimasi = str_replace('.', '', $_POST['estimasi'][$i]);
                $realisasi = str_replace('.', '', $_POST['realisasi'][$i]);
                $persentase = $_POST['persentase'][$i];

                $stmt_detail->execute([$id_laporan, $kode, $nama, $estimasi, $realisasi, $persentase]);
            }
        }

        $pdo->commit();
        echo "<script>alert('Laporan PNBP berhasil disimpan! ✅'); window.location.href='laporan_pnbp.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Gagal menyimpan laporan PNBP: " . $e->getMessage() . "');</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';

$akun_pnbp = [
    ['kode' => '425151', 'nama' => 'Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi'],
    ['kode' => '425211', 'nama' => 'Pendapatan Paspor'],
    ['kode' => '425212', 'nama' => 'Pendapatan Visa'],
    ['kode' => '425213', 'nama' => 'Pendapatan Izin Keimigrasian dan Izin Masuk Kembali (Re-entry permit)'],
    ['kode' => '425214', 'nama' => 'Pendapatan Pelayanan Keimigrasian Lainnya']
];
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Tambah Laporan PNBP 💰</h2>
        <p style="color: var(--text-secondary);">Input estimasi dan realisasi Penerimaan Negara Bukan Pajak.</p>
    </div>
    <a href="laporan_pnbp.php" style="color: var(--text-primary); text-decoration: none; opacity: 0.8; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 30px; font-size: 13px; font-weight: bold; background: var(--bg-secondary);">← Kembali</a>
</div>

<div class="glass panel-utama" style="padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
    
    <form action="" method="POST" id="formPNBP">
        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: var(--text-secondary); font-weight: 600; text-transform: lowercase;">tanggal laporan</label>
                <input type="date" name="tanggal_laporan" class="input-full-width" value="<?= date('Y-m-d') ?>" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); padding: 12px 15px; border-radius: 10px;" required>
            </div>
            <div style="flex: 2;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: var(--text-secondary); font-weight: 600; text-transform: lowercase;">keterangan / periode laporan</label>
                <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                    <input type="month" id="pilih_periode_pnbp" style="flex: 1; background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); padding: 12px 15px; border-radius: 10px;">
                    <button type="button" onclick="generateKeteranganPNBP()" style="padding: 12px 20px; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; font-weight: bold; white-space: nowrap; font-size: 13px; text-transform: lowercase;">
                        📅 generate
                    </button>
                </div>
                <input type="text" name="keterangan" id="input_keterangan_pnbp" class="input-full-width" placeholder="contoh: realisasi pnbp s.d. mei 2026" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); padding: 12px 15px; border-radius: 10px;" required>
                <small style="display:block; margin-top:6px; color: var(--text-muted); font-size: 12px; text-transform: lowercase;">💡 pilih bulan dan tahun, lalu klik generate. atau ketik manual.</small>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 15px 10px; color: var(--text-muted); width: 40%; font-size: 13px; text-transform: lowercase;">uraian (jenis akun)</th>
                        <th style="padding: 15px 10px; color: var(--text-muted); width: 20%; font-size: 13px; text-transform: lowercase;">estimasi (rp)</th>
                        <th style="padding: 15px 10px; color: var(--text-muted); width: 20%; font-size: 13px; text-transform: lowercase;">realisasi (rp)</th>
                        <th style="padding: 15px 10px; color: var(--text-muted); width: 20%; font-size: 13px; text-transform: lowercase;">persentase (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($akun_pnbp as $akun): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 10px;">
                                <input type="hidden" name="kode_akun[]" value="<?= $akun['kode'] ?>">
                                <input type="hidden" name="nama_akun[]" value="<?= htmlspecialchars($akun['nama']) ?>">
                                <strong style="color: var(--text-primary);"><?= $akun['kode'] ?></strong><br>
                                <span style="font-size: 12px; color: var(--text-secondary);"><?= htmlspecialchars($akun['nama']) ?></span>
                            </td>
                            <td style="padding: 10px;">
                                <input type="text" name="estimasi[]" class="input-full-width inp-est" value="0" onkeyup="hitungTotalPNBP()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); font-weight: bold; padding: 12px; border-radius: 8px;">
                            </td>
                            <td style="padding: 10px;">
                                <input type="text" name="realisasi[]" class="input-full-width inp-real" value="0" onkeyup="hitungTotalPNBP()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); font-weight: bold; padding: 12px; border-radius: 8px;">
                            </td>
                            <td style="padding: 10px;">
                                <input type="text" name="persentase[]" class="input-full-width inp-persen" value="0,00%" onkeyup="hitungTotalPNBP()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); font-weight: bold; padding: 12px; border-radius: 8px; text-align: center;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: var(--bg-secondary);">
                        <td style="padding: 15px 10px; font-weight: 900; color: var(--text-primary); text-align: right; letter-spacing: 1px; text-transform: lowercase;">grand total :</td>
                        <td style="padding: 10px;"><input type="text" id="grand_est" value="0" readonly style="width: 100%; background: transparent; color: var(--text-primary); font-weight: bold; font-size: 15px; border:none; padding: 10px;"></td>
                        <td style="padding: 10px;"><input type="text" id="grand_realisasi" value="0" readonly style="width: 100%; background: transparent; color: var(--text-primary); font-weight: bold; font-size: 15px; border:none; padding: 10px;"></td>
                        <td style="padding: 10px;"><input type="text" id="grand_persen" value="0,00%" readonly style="width: 100%; background: transparent; color: var(--text-primary); font-weight: bold; font-size: 15px; border:none; padding: 10px; text-align: center;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="margin-top: 35px; text-align: right;">
            <a href="laporan_pnbp.php" style="text-decoration: none; padding: 12px 25px; border-radius: 30px; margin-right: 12px; color: var(--text-primary); border: 1px solid var(--border-color); font-size: 13px; font-weight: bold;">batal</a>
            <button type="submit" style="padding: 12px 30px; border-radius: 30px; border: none; cursor: pointer; font-weight: bold; background: #ffffff; color: #0a1128; font-size: 13px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">💾 simpan laporan</button>
        </div>
    </form>
</div>

<script>
function generateKeteranganPNBP() {
    const pilih = document.getElementById('pilih_periode_pnbp').value;
    if (!pilih) {
        alert('Silakan pilih bulan dan tahun terlebih dahulu! 📅');
        return;
    }
    const [tahun, bulan] = pilih.split('-');
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const bulanStr = namaBulan[parseInt(bulan) - 1];
    document.getElementById('input_keterangan_pnbp').value = `Realisasi PNBP s.d. ${bulanStr} ${tahun}`;
}

function formatRupiah(angka) {
    let angkaInt = parseInt(angka, 10);
    if (isNaN(angkaInt)) angkaInt = 0;
    var number_string = angkaInt.toString(),
        sisa = number_string.length % 3,
        rupiah = number_string.substr(0, sisa),
        ribuan = number_string.substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return rupiah;
}

function parseAngka(stringAngka) {
    if(!stringAngka) return 0;
    return parseInt(stringAngka.toString().replace(/\./g, ''), 10) || 0;
}

function hitungTotalPNBP() {
    let barisEst = document.querySelectorAll('.inp-est');
    let barisReal = document.querySelectorAll('.inp-real');
    let barisPersen = document.querySelectorAll('.inp-persen');
    
    let grandEst = 0, grandReal = 0;

    for (let i = 0; i < barisEst.length; i++) {
        let e = parseAngka(barisEst[i].value);
        let r = parseAngka(barisReal[i].value);
        
        barisEst[i].value = formatRupiah(e);
        barisReal[i].value = formatRupiah(r);
        
        /** * Logic: Tetap ngitung otomatis pas ngetik, 
         * tapi karena field-nya enabled, user bisa nimpa manual kalau mau.
         */
        if (event && event.target !== barisPersen[i]) {
            let p = e > 0 ? (r / e) * 100 : 0;
            barisPersen[i].value = p.toFixed(2).replace('.', ',') + '%';
        }
        
        grandEst += e; 
        grandReal += r;
    }

    document.getElementById('grand_est').value = formatRupiah(grandEst);
    document.getElementById('grand_realisasi').value = formatRupiah(grandReal);
    
    let grandP = grandEst > 0 ? (grandReal / grandEst) * 100 : 0;
    document.getElementById('grand_persen').value = grandP.toFixed(2).replace('.', ',') + '%';
}

// Jalankan saat load
hitungTotalPNBP();
</script>

<?php include 'layouts/footer.php'; ?>