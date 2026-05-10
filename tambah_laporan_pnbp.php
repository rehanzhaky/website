<?php
session_start();
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
    $tgl_laporan = $_POST['tanggal_laporan'];
    $keterangan  = $_POST['keterangan'];
    
    // Karena ini form PNBP, kita pakai periode mulai & sampai (default ke awal & akhir bulan ini jika formnya cuma 1 tanggal)
    // Di sini kita asumsikan pakai 1 tanggal laporan aja sesuai desain realisasi, tapi disesuaikan ke struktur tabelmu
    $periode_mulai = date('Y-m-01', strtotime($tgl_laporan));
    $periode_sampai = date('Y-m-t', strtotime($tgl_laporan));

    // Bersihkan titik dari inputan 6 Akun PNBP
    $t_131 = str_replace('.', '', $_POST['target_425131']); $r_131 = str_replace('.', '', $_POST['realisasi_425131']); $s_131 = str_replace('.', '', $_POST['sisa_425131']);
    $t_151 = str_replace('.', '', $_POST['target_425151']); $r_151 = str_replace('.', '', $_POST['realisasi_425151']); $s_151 = str_replace('.', '', $_POST['sisa_425151']);
    $t_211 = str_replace('.', '', $_POST['target_425211']); $r_211 = str_replace('.', '', $_POST['realisasi_425211']); $s_211 = str_replace('.', '', $_POST['sisa_425211']);
    $t_212 = str_replace('.', '', $_POST['target_425212']); $r_212 = str_replace('.', '', $_POST['realisasi_425212']); $s_212 = str_replace('.', '', $_POST['sisa_425212']);
    $t_213 = str_replace('.', '', $_POST['target_425213']); $r_213 = str_replace('.', '', $_POST['realisasi_425213']); $s_213 = str_replace('.', '', $_POST['sisa_425213']);
    $t_214 = str_replace('.', '', $_POST['target_425214']); $r_214 = str_replace('.', '', $_POST['realisasi_425214']); $s_214 = str_replace('.', '', $_POST['sisa_425214']);

    $sql = "INSERT INTO laporan_pnbp (
                tanggal_laporan, periode_mulai, periode_sampai, keterangan,
                target_425131, realisasi_425131, sisa_425131,
                target_425151, realisasi_425151, sisa_425151,
                target_425211, realisasi_425211, sisa_425211,
                target_425212, realisasi_425212, sisa_425212,
                target_425213, realisasi_425213, sisa_425213,
                target_425214, realisasi_425214, sisa_425214
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        $tgl_laporan, $periode_mulai, $periode_sampai, $keterangan,
        $t_131, $r_131, $s_131,  $t_151, $r_151, $s_151,
        $t_211, $r_211, $s_211,  $t_212, $r_212, $s_212,
        $t_213, $r_213, $s_213,  $t_214, $r_214, $s_214
    ])) {
        echo "<script>alert('Laporan PNBP berhasil disimpan! 💰'); window.location.href='laporan_pnbp.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan.');</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Tambah Laporan PNBP 💰</h2>
    <p>Input data Target & Realisasi Penerimaan Negara Bukan Pajak.</p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <div style="background: rgba(79, 172, 254, 0.05); border: 1px dashed #4facfe; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4 style="margin: 0 0 5px 0; color: #4facfe;">⚡ Auto-Fill via PDF</h4>
            <p style="margin: 0; font-size: 13px; opacity: 0.8;">Ekstrak angka otomatis dari file laporan PDF PNBP.</p>
        </div>
        <div style="text-align: right;">
            <button type="button" onclick="document.getElementById('file_import_pdf_pnbp').click()" class="btn-navy-pill" style="margin: 0; background: rgba(79, 172, 254, 0.2); color: #4facfe; border: 1px solid #4facfe; cursor: pointer;">
                📄 Upload & Ekstrak
            </button>
            <div id="status_pdf_pnbp" style="font-size: 12px; margin-top: 8px; font-weight: bold;"></div>
            <input type="file" id="file_import_pdf_pnbp" accept=".pdf" style="display: none;" onchange="ekstrakPDFPNBP(this)">
        </div>
    </div>

    <form action="" method="POST">
        
        <div style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 5px;">Kementerian / Lembaga</label>
                    <input type="text" class="input-full-width" value="13 - KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN" readonly style="background: rgba(0,0,0,0.2); color: #aaa; border: 1px solid rgba(255,255,255,0.1);">
                </div>
                <div>
                    <label style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 5px;">Unit Eselon I</label>
                    <input type="text" class="input-full-width" value="03 - DIREKTORAT JENDERAL IMIGRASI" readonly style="background: rgba(0,0,0,0.2); color: #aaa; border: 1px solid rgba(255,255,255,0.1);">
                </div>
            </div>
            <div>
                <label style="font-size: 12px; opacity: 0.7; display: block; margin-bottom: 5px;">Satuan Kerja</label>
                <input type="text" class="input-full-width" value="692823 - KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG" readonly style="background: rgba(0,0,0,0.2); color: #aaa; border: 1px solid rgba(255,255,255,0.1);">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 30px;">
            <div class="form-group form-full">
                <label>Tanggal Laporan</label>
                <input type="date" name="tanggal_laporan" class="input-full-width" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group form-full">
                <label>Keterangan / Periode</label>
                <input type="text" name="keterangan" class="input-full-width" placeholder="Contoh: Laporan PNBP Januari s.d. April 2026" required>
            </div>
        </div>

        <h3 style="border-bottom: 1px solid rgba(79, 172, 254, 0.3); padding-bottom: 10px; color: #4facfe;">Rincian Jenis Akun (Dalam Rupiah)</h3>

        <table style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 40%;">Jenis Akun</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 20%;">Target (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 20%;">Realisasi (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 20%;">Sisa Target (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold; font-size: 13px;">425131 - Pendapatan Sewa Tanah, Gedung, dan Bangunan</td>
                    <td style="padding: 10px;"><input type="text" name="target_425131" id="t_131" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_425131" id="r_131" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_425131" id="s_131" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold; font-size: 13px;">425151 - Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi</td>
                    <td style="padding: 10px;"><input type="text" name="target_425151" id="t_151" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_425151" id="r_151" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_425151" id="s_151" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold; font-size: 13px;">425211 - Pendapatan Paspor</td>
                    <td style="padding: 10px;"><input type="text" name="target_425211" id="t_211" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_425211" id="r_211" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_425211" id="s_211" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold; font-size: 13px;">425212 - Pendapatan Visa</td>
                    <td style="padding: 10px;"><input type="text" name="target_425212" id="t_212" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_425212" id="r_212" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_425212" id="s_212" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold; font-size: 13px;">425213 - Pendapatan Izin Keimigrasian dan Izin Masuk Kembali</td>
                    <td style="padding: 10px;"><input type="text" name="target_425213" id="t_213" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_425213" id="r_213" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_425213" id="s_213" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold; font-size: 13px;">425214 - Pendapatan Pelayanan Keimigrasian Lainnya</td>
                    <td style="padding: 10px;"><input type="text" name="target_425214" id="t_214" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_425214" id="r_214" class="input-full-width angka" value="0" onkeyup="hitungPNBP()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_425214" id="s_214" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="background: rgba(79, 172, 254, 0.1);">
                    <td style="padding: 15px 10px; font-weight: 900; color: #4facfe; text-align: right; font-size: 16px;">TOTAL :</td>
                    <td style="padding: 10px;"><input type="text" id="total_target" class="input-full-width" value="0" readonly style="background: transparent; color: #4facfe; font-weight: bold; border-color: rgba(79, 172, 254, 0.3);"></td>
                    <td style="padding: 10px;"><input type="text" id="total_realisasi" class="input-full-width" value="0" readonly style="background: transparent; color: #4facfe; font-weight: bold; border-color: rgba(79, 172, 254, 0.3);"></td>
                    <td style="padding: 10px;"><input type="text" id="total_sisa" class="input-full-width" value="0" readonly style="background: transparent; color: #4facfe; font-weight: bold; border-color: rgba(79, 172, 254, 0.3);"></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <a href="laporan_pnbp.php" class="btn-outline-danger" style="text-decoration: none; padding: 12px 20px; border-radius: 8px; margin-right: 10px;">Batal</a>
            <button type="submit" class="btn-solid-primary" style="padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; background: #4facfe; color: #0a192f;">💾 Simpan Laporan</button>
        </div>
    </form>
</div>

<script>
// ------------------------------------------------------------------
// LOGIKA JAVASCRIPT AJAX UNTUK EKSTRAK PDF PNBP
// ------------------------------------------------------------------
function ekstrakPDFPNBP(input) {
    if (input.files && input.files[0]) {
        let formData = new FormData();
        formData.append("file_pdf", input.files[0]);
        
        let statusObj = document.getElementById('status_pdf_pnbp');
        statusObj.innerText = "⏳ Membaca & Ekstrak Kode Akun...";
        statusObj.style.color = "#ffb86c";

        fetch('proses_baca_pdf.php', { 
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                console.log("=== TEKS MENTAH PNBP ===");
                console.log(data.teks_mentah);

                if(data.tipe_pdf !== 'pnbp_akun') {
                    statusObj.innerText = "⚠️ Ini sepertinya bukan PDF PNBP!";
                    statusObj.style.color = "#ffb86c";
                    return;
                }

                statusObj.innerText = "✅ Berhasil diekstrak!";
                statusObj.style.color = "#64ffda";
                
                // Isi Form Otomatis berdasarkan respon backend
                document.getElementById('t_131').value = formatRupiah(data.t_131 || 0);
                document.getElementById('r_131').value = formatRupiah(data.r_131 || 0);
                
                document.getElementById('t_151').value = formatRupiah(data.t_151 || 0);
                document.getElementById('r_151').value = formatRupiah(data.r_151 || 0);
                
                document.getElementById('t_211').value = formatRupiah(data.t_211 || 0);
                document.getElementById('r_211').value = formatRupiah(data.r_211 || 0);
                
                document.getElementById('t_212').value = formatRupiah(data.t_212 || 0);
                document.getElementById('r_212').value = formatRupiah(data.r_212 || 0);
                
                document.getElementById('t_213').value = formatRupiah(data.t_213 || 0);
                document.getElementById('r_213').value = formatRupiah(data.r_213 || 0);
                
                document.getElementById('t_214').value = formatRupiah(data.t_214 || 0);
                document.getElementById('r_214').value = formatRupiah(data.r_214 || 0);

                // Paksa kalkulator ngitung sisa dan total
                hitungPNBP(); 

            } else {
                statusObj.innerText = "❌ Gagal: " + data.pesan;
                statusObj.style.color = "#ff4c4c";
            }
        })
        .catch(error => {
            statusObj.innerText = "❌ Terjadi kesalahan jaringan.";
            statusObj.style.color = "#ff4c4c";
            console.error(error);
        });
    }
}

function formatRupiah(angka) {
    var number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    // Tambahin minus kalau angkanya negatif (Misal Realisasi > Target)
    return angka.toString().includes('-') ? '-' + rupiah : rupiah;
}

function parseAngka(stringAngka) {
    if(!stringAngka) return 0;
    return parseInt(stringAngka.replace(/\./g, '')) || 0;
}

// Fungsi Hitung Baris & Total
function hitung(id_target, id_realisasi, id_sisa) {
    let t = parseAngka(document.getElementById(id_target).value);
    let r = parseAngka(document.getElementById(id_realisasi).value);
    // Sisa PNBP = Target dikurangi Realisasi (Berapa lagi target yg harus dicapai)
    let s = t - r; 
    document.getElementById(id_sisa).value = formatRupiah(s);
    return { target: t, realisasi: r, sisa: s };
}

function hitungPNBP() {
    let inputs = document.querySelectorAll('.angka');
    inputs.forEach(input => { input.value = formatRupiah(input.value); });

    let b1 = hitung('t_131', 'r_131', 's_131');
    let b2 = hitung('t_151', 'r_151', 's_151');
    let b3 = hitung('t_211', 'r_211', 's_211');
    let b4 = hitung('t_212', 'r_212', 's_212');
    let b5 = hitung('t_213', 'r_213', 's_213');
    let b6 = hitung('t_214', 'r_214', 's_214');

    let total_t = b1.target + b2.target + b3.target + b4.target + b5.target + b6.target;
    let total_r = b1.realisasi + b2.realisasi + b3.realisasi + b4.realisasi + b5.realisasi + b6.realisasi;
    let total_s = b1.sisa + b2.sisa + b3.sisa + b4.sisa + b5.sisa + b6.sisa;

    document.getElementById('total_target').value = formatRupiah(total_t);
    document.getElementById('total_realisasi').value = formatRupiah(total_r);
    document.getElementById('total_sisa').value = formatRupiah(total_s);
}
</script>

<?php include 'layouts/footer.php'; ?>