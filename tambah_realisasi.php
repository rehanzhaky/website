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
    $tipe_laporan = 'jenis_belanja'; 

    $p_pegawai = str_replace('.', '', $_POST['pagu_pegawai']);
    $r_pegawai = str_replace('.', '', $_POST['realisasi_pegawai']);
    $s_pegawai = str_replace('.', '', $_POST['sisa_pegawai']);

    $p_barang = str_replace('.', '', $_POST['pagu_barang']);
    $r_barang = str_replace('.', '', $_POST['realisasi_barang']);
    $s_barang = str_replace('.', '', $_POST['sisa_barang']);

    $p_modal = str_replace('.', '', $_POST['pagu_modal']);
    $r_modal = str_replace('.', '', $_POST['realisasi_modal']);
    $s_modal = str_replace('.', '', $_POST['sisa_modal']);

    $sql = "INSERT INTO laporan_realisasi (tanggal_laporan, keterangan, tipe_laporan, 
            pagu_pegawai, realisasi_pegawai, sisa_pegawai, 
            pagu_barang, realisasi_barang, sisa_barang, 
            pagu_modal, realisasi_modal, sisa_modal) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$tgl_laporan, $keterangan, $tipe_laporan, 
        $p_pegawai, $r_pegawai, $s_pegawai, 
        $p_barang, $r_barang, $s_barang, 
        $p_modal, $r_modal, $s_modal])) {
        echo "<script>alert('Laporan Realisasi Belanja berhasil disimpan! ✅'); window.location.href='realisasi_anggaran.php?tab=jenis_belanja';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan.');</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Tambah Laporan Realisasi 📝</h2>
    <p>Input data realisasi Satker per <strong>Jenis Belanja</strong>.</p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <div style="background: rgba(100, 255, 218, 0.05); border: 1px dashed #64ffda; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4 style="margin: 0 0 5px 0; color: #64ffda;">⚡ Auto-Fill via PDF</h4>
            <p style="margin: 0; font-size: 13px; opacity: 0.8;">Biar sistem yang ketik angkanya otomatis dari laporan PDF Jenis Belanja!</p>
        </div>
        <div style="text-align: right;">
            <button type="button" onclick="document.getElementById('file_import_pdf_jb').click()" class="btn-navy-pill" style="margin: 0; background: rgba(100, 255, 218, 0.2); color: #64ffda; border: 1px solid #64ffda; cursor: pointer;">
                📄 Upload & Ekstrak
            </button>
            <div id="status_pdf_jb" style="font-size: 12px; margin-top: 8px; font-weight: bold;"></div>
            <input type="file" id="file_import_pdf_jb" accept=".pdf" style="display: none;" onchange="ekstrakPDFJenisBelanja(this)">
        </div>
    </div>
    <form action="" method="POST">
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 30px;">
            <div class="form-group form-full">
                <label>Tanggal Laporan</label>
                <input type="date" name="tanggal_laporan" class="input-full-width" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group form-full">
                <label>Keterangan / Judul Laporan</label>
                <input type="text" name="keterangan" class="input-full-width" placeholder="Contoh: Realisasi Anggaran DIPA Bulan April 2026" required>
            </div>
        </div>

        <h3 style="border-bottom: 1px solid rgba(100, 255, 218, 0.3); padding-bottom: 10px; color: #64ffda;">Rincian Anggaran (Otomatis Dihitung)</h3>

        <table style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Keterangan</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Pagu Anggaran (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Realisasi (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Sisa Anggaran (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold;">👨‍💼 Belanja Pegawai</td>
                    <td style="padding: 10px;"><input type="text" name="pagu_pegawai" id="pagu_pegawai" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_pegawai" id="realisasi_pegawai" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_pegawai" id="sisa_pegawai" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold;">📦 Belanja Barang</td>
                    <td style="padding: 10px;"><input type="text" name="pagu_barang" id="pagu_barang" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_barang" id="realisasi_barang" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_barang" id="sisa_barang" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px 10px; font-weight: bold;">🏢 Belanja Modal</td>
                    <td style="padding: 10px;"><input type="text" name="pagu_modal" id="pagu_modal" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 10px;"><input type="text" name="realisasi_modal" id="realisasi_modal" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 10px;"><input type="text" name="sisa_modal" id="sisa_modal" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>

                <tr style="background: rgba(100, 255, 218, 0.1);">
                    <td style="padding: 15px 10px; font-weight: 800; color: #64ffda; text-align: right;">GRAND TOTAL :</td>
                    <td style="padding: 10px;"><input type="text" id="total_pagu" class="input-full-width" value="0" readonly style="background: transparent; color: #64ffda; font-weight: bold; border-color: rgba(100,255,218,0.3);"></td>
                    <td style="padding: 10px;"><input type="text" id="total_realisasi" class="input-full-width" value="0" readonly style="background: transparent; color: #64ffda; font-weight: bold; border-color: rgba(100,255,218,0.3);"></td>
                    <td style="padding: 10px;"><input type="text" id="total_sisa" class="input-full-width" value="0" readonly style="background: transparent; color: #64ffda; font-weight: bold; border-color: rgba(100,255,218,0.3);"></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <a href="realisasi_anggaran.php?tab=jenis_belanja" class="btn-outline-danger" style="text-decoration: none; padding: 12px 20px; border-radius: 8px; margin-right: 10px;">Batal</a>
            <button type="submit" class="btn-solid-primary" style="padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; background: #4facfe; color: #0a192f;">💾 Simpan Laporan</button>
        </div>
    </form>
</div>

<script>
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
    return rupiah;
}

function parseAngka(stringAngka) {
    if(!stringAngka) return 0;
    return parseInt(stringAngka.replace(/\./g, '')) || 0;
}

// ------------------------------------------------------------------
// LOGIKA JAVASCRIPT AJAX UNTUK EKSTRAK PDF JENIS BELANJA
// ------------------------------------------------------------------
function ekstrakPDFJenisBelanja(input) {
    if (input.files && input.files[0]) {
        let formData = new FormData();
        formData.append("file_pdf", input.files[0]);
        
        let statusObj = document.getElementById('status_pdf_jb');
        statusObj.innerText = "⏳ Membaca PDF...";
        statusObj.style.color = "#ffb86c";

        fetch('proses_baca_pdf.php', { 
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                statusObj.innerText = "✅ Berhasil diekstrak!";
                statusObj.style.color = "#64ffda";
                
                // Di proses_baca_pdf.php, kalau PDF-nya Jenis Belanja, 
                // data dilempar ke variabel rm_ jadi kita ambil dari sana
                document.getElementById('pagu_pegawai').value = formatRupiah(data.rm_p_peg || 0);
                document.getElementById('realisasi_pegawai').value = formatRupiah(data.rm_r_peg || 0);
                
                document.getElementById('pagu_barang').value = formatRupiah(data.rm_p_brg || 0);
                document.getElementById('realisasi_barang').value = formatRupiah(data.rm_r_brg || 0);
                
                document.getElementById('pagu_modal').value = formatRupiah(data.rm_p_mod || 0);
                document.getElementById('realisasi_modal').value = formatRupiah(data.rm_r_mod || 0);

                // Jalankan kalkulator otomatis
                hitungSemua(); 

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

// ------------------------------------------------------------------
// LOGIKA KALKULATOR OTOMATIS
// ------------------------------------------------------------------
function hitungSemua() {
    let inputs = document.querySelectorAll('.angka');
    
    inputs.forEach(function(input) {
        input.value = formatRupiah(input.value);
    });

    let p_peg = parseAngka(document.getElementById('pagu_pegawai').value);
    let r_peg = parseAngka(document.getElementById('realisasi_pegawai').value);
    let s_peg = p_peg - r_peg;
    document.getElementById('sisa_pegawai').value = formatRupiah(s_peg);

    let p_brg = parseAngka(document.getElementById('pagu_barang').value);
    let r_brg = parseAngka(document.getElementById('realisasi_barang').value);
    let s_brg = p_brg - r_brg;
    document.getElementById('sisa_barang').value = formatRupiah(s_brg);

    let p_mod = parseAngka(document.getElementById('pagu_modal').value);
    let r_mod = parseAngka(document.getElementById('realisasi_modal').value);
    let s_mod = p_mod - r_mod;
    document.getElementById('sisa_modal').value = formatRupiah(s_mod);

    let total_pagu = p_peg + p_brg + p_mod;
    let total_realisasi = r_peg + r_brg + r_mod;
    let total_sisa = s_peg + s_brg + s_mod;

    document.getElementById('total_pagu').value = formatRupiah(total_pagu);
    document.getElementById('total_realisasi').value = formatRupiah(total_realisasi);
    document.getElementById('total_sisa').value = formatRupiah(total_sisa);
}
</script>

<?php include 'layouts/footer.php'; ?>