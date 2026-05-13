<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Logic Simpan Data ke Database (Laporan Realisasi Per Bidang)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_laporan = $_POST['tanggal_laporan'];
    $keterangan  = $_POST['keterangan'];
    $seksi       = $_POST['seksi'];

    try {
        $pdo->beginTransaction();
        
        // Simpan Master
        $stmt_master = $pdo->prepare("INSERT INTO laporan_realisasi_bidang (tanggal_laporan, seksi, keterangan) VALUES (?, ?, ?)");
        $stmt_master->execute([$tgl_laporan, $seksi, $keterangan]);
        $id_laporan = $pdo->lastInsertId();

        // Simpan Detail Komponen
        if (isset($_POST['kode'])) {
            $stmt_detail = $pdo->prepare("INSERT INTO laporan_realisasi_bidang_detail (id_laporan, kode_komponen, judul_komponen, pagu, realisasi, sisa) VALUES (?, ?, ?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['kode']); $i++) {
                $p = str_replace('.', '', $_POST['pagu'][$i]);
                $r = str_replace('.', '', $_POST['realisasi'][$i]);
                $s = str_replace('.', '', $_POST['sisa'][$i]);
                $stmt_detail->execute([$id_laporan, $_POST['kode'][$i], $_POST['judul'][$i], $p, $r, $s]);
            }
        }
        
        $pdo->commit();
        echo "<script>alert('Laporan Realisasi berhasil disimpan! ✅'); window.location.href='realisasi_anggaran.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Gagal: " . $e->getMessage() . "');</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Tambah Realisasi Per Bidang 📊</h2>
    <p>Input manual target dan capaian anggaran sesuai komponen Seksi.</p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    <form action="" method="POST" id="formRealisasi">
        
        <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 30px;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px;">Tanggal Laporan</label>
                <input type="date" name="tanggal_laporan" class="input-full-width" style="width: 100%; box-sizing: border-box;" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px;">Pilih Seksi / Bidang</label>
                <select name="seksi" id="seksi_dropdown" style="width: 100%; box-sizing: border-box; background: rgba(0,0,0,0.2); border: 1px solid rgba(100,255,218,0.5); color: #fff; padding: 12px; border-radius: 5px; font-size: 14px;" onchange="generateTabel()" required>
                    <option value="">-- Pilih Seksi --</option>
                    <option value="LANTASKIM">LANTASKIM</option>
                    <option value="INTALTUSKIM">INTALTUSKIM</option>
                    <option value="INTELDAKIM">INTELDAKIM</option>
                    <option value="PEMERIKSAAN KEIMIGRASIAN (TPI)">PEMERIKSAAN KEIMIGRASIAN (TPI)</option>
                    <option value="URUSAN UMUM">URUSAN UMUM</option>
                    <option value="TIKIM">TIKIM</option>
                    <option value="LAYANAN PERKANTORAN">LAYANAN PERKANTORAN</option>
                    <option value="BELANJA MODAL">BELANJA MODAL</option>
                    <option value="URUSAN KEPEGAWAIAN">URUSAN KEPEGAWAIAN</option>
                    <option value="URUSAN KEUANGAN">URUSAN KEUANGAN</option>
                    <option value="LAYANAN MANAJEMEN KINERJA INTERNAL">LAYANAN MANAJEMEN KINERJA INTERNAL</option>
                </select>
            </div>
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px;">Keterangan / Periode</label>
                <input type="text" name="keterangan" class="input-full-width" style="width: 100%; box-sizing: border-box;" placeholder="Contoh: Realisasi s.d. Mei 2026" required>
            </div>
        </div>

        <div id="area_tabel" style="display: none;">
            <h3 style="border-bottom: 1px solid rgba(100, 255, 218, 0.3); padding-bottom: 10px; color: #64ffda; margin-bottom: 20px;">Rincian Komponen Anggaran</h3>
            
            <table class="table-minimal" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(100, 255, 218, 0.2);">
                        <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 35%;">Realisasi Per Bidang</th>
                        <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 20%;">Pagu (Rp)</th>
                        <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 20%;">Realisasi (Rp)</th>
                        <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Sisa Anggaran (Rp)</th>
                    </tr>
                </thead>
                <tbody id="tbody_dinamis"></tbody>
            </table>

            <div style="margin-top: 30px; text-align: right;">
                <a href="realisasi_anggaran.php" class="btn-outline-danger" style="text-decoration: none; padding: 12px 20px; border-radius: 8px; margin-right: 10px;">Batal</a>
                <button type="submit" class="btn-solid-primary" style="padding: 12px 35px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; background: #4facfe; color: #0a192f;">💾 Simpan Laporan</button>
            </div>
        </div>

        <div id="pesan_kosong" style="text-align: center; padding: 50px; color: rgba(255,255,255,0.5);">
            👆 Silakan pilih <strong>Seksi / Bidang</strong> untuk memunculkan tabel rincian.
        </div>
    </form>
</div>

<script>
// Data Komponen sesuai request terakhir kamu
const dataSeksi = {
    "LANTASKIM": [{kode: "BAA 001", judul: "Layanan Penerbitan Dokumen Perjalanan RI"}],
    "INTALTUSKIM": [{kode: "BAA 002", judul: "Layanan Penerbitan Izin Tinggal dan Status Keimigrasian"}],
    "INTELDAKIM": [
        {kode: "BHB U11", judul: "Operasi intelejen Keimigrasian di Wilayah Barat"},
        {kode: "BHB U14", judul: "Operasi Gabungan di Wilayah Barat"},
        {kode: "BHB U17", judul: "Penyidikan Tindak Pidana Keimigrasian di Wilayah Barat"},
        {kode: "BIB 001", judul: "Penyidikan Tindakan Administratif Keimigrasian"},
        {kode: "BKA 001", judul: "Pengawasan Keimigrasian"},
        {kode: "BKA 002", judul: "Pembentukan dan Pembinaan Desa Binaan Imigrasi"},
        {kode: "QHB U02", judul: "Operasi di Wilayah Mandiri"}
    ],
    "PEMERIKSAAN KEIMIGRASIAN (TPI)": [
        {kode: "QIB 002", judul: "Pemeriksaan Keimigrasian di TPI"},
        {kode: "BIF U11", judul: "Pemeriksaan Keimigrasian Non Reguler Wilayah Barat"}
    ],
    "URUSAN UMUM": [{kode: "EBA Z07", judul: "Layanan BMN"}, {kode: "EBA 962", judul: "Layanan Umum"}],
    "TIKIM": [{kode: "EBA 963", judul: "Layanan Data dan Informasi"}],
    "LAYANAN PERKANTORAN": [{kode: "EBA 994", judul: "Layanan Perkantoran"}],
    "BELANJA MODAL": [{kode: "EBA 971", judul: "Layanan Prasarana Internal"}, {kode: "EBA 951", judul: "Layanan Sarana Internal"}],
    "URUSAN KEPEGAWAIAN": [{kode: "EBC 954", judul: "Layanan Manajemen SDM"}],
    "URUSAN KEUANGAN": [
        {kode: "EBD 001", judul: "Koordinasi dan Konsultasi Perencanaan dan Pelaksanaan Anggaran"},
        {kode: "EBD Z27", judul: "Layanan Manajemen Keuangan"}
    ],
    "LAYANAN MANAJEMEN KINERJA INTERNAL": [{kode: "EBD Z32", judul: "Layanan Reformasi Kerja"}]
};

function formatRupiah(angka) {
    if (!angka) return "0";
    let val = angka.toString().replace(/[^0-9]/g, '');
    return val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function parseAngka(str) {
    if (!str) return 0;
    return parseInt(str.toString().replace(/\./g, '')) || 0;
}

function generateTabel() {
    const seksi = document.getElementById('seksi_dropdown').value;
    const tbody = document.getElementById('tbody_dinamis');
    const area = document.getElementById('area_tabel');
    const msg = document.getElementById('pesan_kosong');
    const list = dataSeksi[seksi] || [];

    tbody.innerHTML = ''; 
    if (seksi && list.length > 0) {
        area.style.display = 'block';
        msg.style.display = 'none';

        list.forEach((item) => {
            tbody.innerHTML += `
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 15px 10px;">
                        <input type="hidden" name="kode[]" value="${item.kode}">
                        <input type="hidden" name="judul[]" value="${item.judul}">
                        <span style="color: #64ffda; font-weight: bold; font-family: monospace;">${item.kode}</span><br>
                        <span style="font-size: 13px; color: #ccd6f6; opacity: 0.8;">${item.judul}</span>
                    </td>
                    <td><input type="text" name="pagu[]" class="input-full-width inp-pagu" value="0" onkeyup="hitungTotal(this)"></td>
                    <td><input type="text" name="realisasi[]" class="input-full-width inp-real" value="0" onkeyup="hitungTotal(this)"></td>
                    <td><input type="text" name="sisa[]" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05); border:none; color: #8892b0; cursor: not-allowed;"></td>
                </tr>`;
        });
    } else {
        area.style.display = 'none';
        msg.style.display = 'block';
    }
}

function hitungTotal(el) {
    let tr = el.closest('tr');
    let pagu = parseAngka(tr.querySelector('.inp-pagu').value);
    let real = parseAngka(tr.querySelector('.inp-real').value);
    let sisa = pagu - real;

    tr.querySelector('.inp-pagu').value = formatRupiah(pagu);
    tr.querySelector('.inp-real').value = formatRupiah(real);
    tr.querySelector('input[name="sisa[]"]').value = formatRupiah(sisa);
}
</script>

<?php include 'layouts/footer.php'; ?>