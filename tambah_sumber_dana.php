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
    $tipe_laporan = 'sumber_dana'; 

    $rm_p_peg = str_replace('.', '', $_POST['rm_pagu_pegawai']);
    $rm_r_peg = str_replace('.', '', $_POST['rm_realisasi_pegawai']);
    $rm_s_peg = str_replace('.', '', $_POST['rm_sisa_pegawai']);
    $rm_p_brg = str_replace('.', '', $_POST['rm_pagu_barang']);
    $rm_r_brg = str_replace('.', '', $_POST['rm_realisasi_barang']);
    $rm_s_brg = str_replace('.', '', $_POST['rm_sisa_barang']);
    $rm_p_mod = str_replace('.', '', $_POST['rm_pagu_modal']);
    $rm_r_mod = str_replace('.', '', $_POST['rm_realisasi_modal']);
    $rm_s_mod = str_replace('.', '', $_POST['rm_sisa_modal']);

    $pnbp_p_peg = str_replace('.', '', $_POST['pnbp_pagu_pegawai']);
    $pnbp_r_peg = str_replace('.', '', $_POST['pnbp_realisasi_pegawai']);
    $pnbp_s_peg = str_replace('.', '', $_POST['pnbp_sisa_pegawai']);
    $pnbp_p_brg = str_replace('.', '', $_POST['pnbp_pagu_barang']);
    $pnbp_r_brg = str_replace('.', '', $_POST['pnbp_realisasi_barang']);
    $pnbp_s_brg = str_replace('.', '', $_POST['pnbp_sisa_barang']);
    $pnbp_p_mod = str_replace('.', '', $_POST['pnbp_pagu_modal']);
    $pnbp_r_mod = str_replace('.', '', $_POST['pnbp_realisasi_modal']);
    $pnbp_s_mod = str_replace('.', '', $_POST['pnbp_sisa_modal']);

    $sql = "INSERT INTO laporan_realisasi (
                tanggal_laporan, keterangan, tipe_laporan, 
                rm_pagu_pegawai, rm_realisasi_pegawai, rm_sisa_pegawai,
                rm_pagu_barang, rm_realisasi_barang, rm_sisa_barang,
                rm_pagu_modal, rm_realisasi_modal, rm_sisa_modal,
                pnbp_pagu_pegawai, pnbp_realisasi_pegawai, pnbp_sisa_pegawai,
                pnbp_pagu_barang, pnbp_realisasi_barang, pnbp_sisa_barang,
                pnbp_pagu_modal, pnbp_realisasi_modal, pnbp_sisa_modal
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        $tgl_laporan, $keterangan, $tipe_laporan,
        $rm_p_peg, $rm_r_peg, $rm_s_peg, $rm_p_brg, $rm_r_brg, $rm_s_brg, $rm_p_mod, $rm_r_mod, $rm_s_mod,
        $pnbp_p_peg, $pnbp_r_peg, $pnbp_s_peg, $pnbp_p_brg, $pnbp_r_brg, $pnbp_s_brg, $pnbp_p_mod, $pnbp_r_mod, $pnbp_s_mod
    ])) {
        echo "<script>alert('Laporan Sumber Dana berhasil disimpan! ✅'); window.location.href='realisasi_anggaran.php?tab=sumber_dana';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan.');</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Tambah Laporan Realisasi 🏦</h2>
    <p>Input data realisasi Satker per <strong>Sumber Dana (RM & PNBP)</strong>.</p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <div style="background: rgba(100, 255, 218, 0.05); border: 1px dashed #64ffda; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4 style="margin: 0 0 5px 0; color: #64ffda;">⚡ Auto-Fill via PDF</h4>
            <p style="margin: 0; font-size: 13px; opacity: 0.8;">Biar sistem yang ketik angkanya otomatis dari laporan PDF!</p>
        </div>
        <div style="text-align: right;">
            <button type="button" onclick="document.getElementById('file_import_pdf').click()" class="btn-navy-pill" style="margin: 0; background: rgba(100, 255, 218, 0.2); color: #64ffda; border: 1px solid #64ffda; cursor: pointer;">
                📄 Upload & Ekstrak
            </button>
            <div id="status_pdf" style="font-size: 12px; margin-top: 8px; font-weight: bold;"></div>
            <input type="file" id="file_import_pdf" accept=".pdf" style="display: none;" onchange="ekstrakPDF(this)">
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
                <input type="text" name="keterangan" class="input-full-width" placeholder="Contoh: Realisasi Sumber Dana April 2026" required>
            </div>
        </div>

        <h3 style="background: rgba(167, 139, 250, 0.15); padding: 10px; border-radius: 5px; color: #c4b5fd; font-size: 15px;">(A) RUPIAH MURNI</h3>
        <table style="width: 100%; text-align: left; border-collapse: collapse; margin-bottom: 25px;">
            <thead>
                <tr>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Keterangan</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Pagu (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Realisasi (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Sisa (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">👨‍💼 Pegawai</td>
                    <td style="padding: 5px;"><input type="text" name="rm_pagu_pegawai" id="rm_pagu_pegawai" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="rm_realisasi_pegawai" id="rm_realisasi_pegawai" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="rm_sisa_pegawai" id="rm_sisa_pegawai" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">📦 Barang</td>
                    <td style="padding: 5px;"><input type="text" name="rm_pagu_barang" id="rm_pagu_barang" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="rm_realisasi_barang" id="rm_realisasi_barang" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="rm_sisa_barang" id="rm_sisa_barang" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">🏢 Modal</td>
                    <td style="padding: 5px;"><input type="text" name="rm_pagu_modal" id="rm_pagu_modal" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="rm_realisasi_modal" id="rm_realisasi_modal" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="rm_sisa_modal" id="rm_sisa_modal" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>
                <tr style="background: rgba(167, 139, 250, 0.05);">
                    <td style="padding: 10px; font-weight: bold; color: #c4b5fd; text-align: right;">Total RM :</td>
                    <td style="padding: 5px;"><input type="text" id="rm_sub_pagu" class="input-full-width" value="0" readonly style="border:none; background:transparent; color:#c4b5fd; font-weight:bold;"></td>
                    <td style="padding: 5px;"><input type="text" id="rm_sub_realisasi" class="input-full-width" value="0" readonly style="border:none; background:transparent; color:#c4b5fd; font-weight:bold;"></td>
                    <td style="padding: 5px;"><input type="text" id="rm_sub_sisa" class="input-full-width" value="0" readonly style="border:none; background:transparent; color:#c4b5fd; font-weight:bold;"></td>
                </tr>
            </tbody>
        </table>

        <h3 style="background: rgba(34, 211, 238, 0.15); padding: 10px; border-radius: 5px; color: #67e8f9; font-size: 15px;">(D) PENERIMAAN NEGARA BUKAN PAJAK (PNBP)</h3>
        <table style="width: 100%; text-align: left; border-collapse: collapse; margin-bottom: 25px;">
             <thead>
                <tr>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Keterangan</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Pagu (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Realisasi (Rp)</th>
                    <th style="padding: 10px; color: rgba(255,255,255,0.7); width: 25%;">Sisa (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">👨‍💼 Pegawai</td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_pagu_pegawai" id="pnbp_pagu_pegawai" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_realisasi_pegawai" id="pnbp_realisasi_pegawai" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_sisa_pegawai" id="pnbp_sisa_pegawai" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">📦 Barang</td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_pagu_barang" id="pnbp_pagu_barang" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_realisasi_barang" id="pnbp_realisasi_barang" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_sisa_barang" id="pnbp_sisa_barang" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>
                <tr>
                    <td style="padding: 10px; font-weight: bold;">🏢 Modal</td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_pagu_modal" id="pnbp_pagu_modal" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_realisasi_modal" id="pnbp_realisasi_modal" class="input-full-width angka" value="0" onkeyup="hitungSemua()"></td>
                    <td style="padding: 5px;"><input type="text" name="pnbp_sisa_modal" id="pnbp_sisa_modal" class="input-full-width" value="0" readonly style="background: rgba(255,255,255,0.05);"></td>
                </tr>
                 <tr style="background: rgba(34, 211, 238, 0.05);">
                    <td style="padding: 10px; font-weight: bold; color: #67e8f9; text-align: right;">Total PNBP :</td>
                    <td style="padding: 5px;"><input type="text" id="pnbp_sub_pagu" class="input-full-width" value="0" readonly style="border:none; background:transparent; color:#67e8f9; font-weight:bold;"></td>
                    <td style="padding: 5px;"><input type="text" id="pnbp_sub_realisasi" class="input-full-width" value="0" readonly style="border:none; background:transparent; color:#67e8f9; font-weight:bold;"></td>
                    <td style="padding: 5px;"><input type="text" id="pnbp_sub_sisa" class="input-full-width" value="0" readonly style="border:none; background:transparent; color:#67e8f9; font-weight:bold;"></td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background: rgba(100, 255, 218, 0.15); border: 1px solid #64ffda;">
                <td style="padding: 15px 10px; font-weight: 900; color: #64ffda; text-align: right; width: 25%; font-size: 16px;">GRAND TOTAL :</td>
                <td style="padding: 10px; width: 25%;"><input type="text" id="grand_pagu" class="input-full-width" value="0" readonly style="background: transparent; color: #64ffda; font-weight: 900; font-size: 16px; border:none;"></td>
                <td style="padding: 10px; width: 25%;"><input type="text" id="grand_realisasi" class="input-full-width" value="0" readonly style="background: transparent; color: #64ffda; font-weight: 900; font-size: 16px; border:none;"></td>
                <td style="padding: 10px; width: 25%;"><input type="text" id="grand_sisa" class="input-full-width" value="0" readonly style="background: transparent; color: #64ffda; font-weight: 900; font-size: 16px; border:none;"></td>
            </tr>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <a href="realisasi_anggaran.php?tab=sumber_dana" class="btn-outline-danger" style="text-decoration: none; padding: 12px 20px; border-radius: 8px; margin-right: 10px;">Batal</a>
            <button type="submit" class="btn-solid-primary" style="padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; background: #002545; color: #ffffff;">💾 Simpan Laporan</button>
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

function ekstrakPDF(input) {
    if (input.files && input.files[0]) {
        let formData = new FormData();
        formData.append("file_pdf", input.files[0]);
        
        let statusObj = document.getElementById('status_pdf');
        statusObj.innerText = "⏳ Sedang mengekstrak angka...";
        statusObj.style.color = "#ffb86c";

        fetch('proses_baca_pdf.php', { 
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                console.log("=== TEKS MENTAH PDF ===");
                console.log(data.teks_mentah);
    
                statusObj.innerText = "✅ Berhasil diekstrak! Cek kolom di bawah.";
                
                document.getElementById('rm_pagu_pegawai').value = formatRupiah(data.rm_p_peg || 0);
                document.getElementById('rm_realisasi_pegawai').value = formatRupiah(data.rm_r_peg || 0);
                document.getElementById('rm_pagu_barang').value = formatRupiah(data.rm_p_brg || 0);
                document.getElementById('rm_realisasi_barang').value = formatRupiah(data.rm_r_brg || 0);
                document.getElementById('rm_pagu_modal').value = formatRupiah(data.rm_p_mod || 0);
                document.getElementById('rm_realisasi_modal').value = formatRupiah(data.rm_r_mod || 0);

                document.getElementById('pnbp_pagu_pegawai').value = formatRupiah(data.pnbp_p_peg || 0);
                document.getElementById('pnbp_realisasi_pegawai').value = formatRupiah(data.pnbp_r_peg || 0);
                document.getElementById('pnbp_pagu_barang').value = formatRupiah(data.pnbp_p_brg || 0);
                document.getElementById('pnbp_realisasi_barang').value = formatRupiah(data.pnbp_r_brg || 0);
                document.getElementById('pnbp_pagu_modal').value = formatRupiah(data.pnbp_p_mod || 0);
                document.getElementById('pnbp_realisasi_modal').value = formatRupiah(data.pnbp_r_mod || 0);

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

function hitungSemua() {
    let inputs = document.querySelectorAll('.angka');
    inputs.forEach(input => { input.value = formatRupiah(input.value); });

    let rm_p_peg = parseAngka(document.getElementById('rm_pagu_pegawai').value);
    let rm_r_peg = parseAngka(document.getElementById('rm_realisasi_pegawai').value);
    document.getElementById('rm_sisa_pegawai').value = formatRupiah(rm_p_peg - rm_r_peg);

    let rm_p_brg = parseAngka(document.getElementById('rm_pagu_barang').value);
    let rm_r_brg = parseAngka(document.getElementById('rm_realisasi_barang').value);
    document.getElementById('rm_sisa_barang').value = formatRupiah(rm_p_brg - rm_r_brg);

    let rm_p_mod = parseAngka(document.getElementById('rm_pagu_modal').value);
    let rm_r_mod = parseAngka(document.getElementById('rm_realisasi_modal').value);
    document.getElementById('rm_sisa_modal').value = formatRupiah(rm_p_mod - rm_r_mod);

    let rm_sub_p = rm_p_peg + rm_p_brg + rm_p_mod;
    let rm_sub_r = rm_r_peg + rm_r_brg + rm_r_mod;
    document.getElementById('rm_sub_pagu').value = formatRupiah(rm_sub_p);
    document.getElementById('rm_sub_realisasi').value = formatRupiah(rm_sub_r);
    document.getElementById('rm_sub_sisa').value = formatRupiah(rm_sub_p - rm_sub_r);

    let pnbp_p_peg = parseAngka(document.getElementById('pnbp_pagu_pegawai').value);
    let pnbp_r_peg = parseAngka(document.getElementById('pnbp_realisasi_pegawai').value);
    document.getElementById('pnbp_sisa_pegawai').value = formatRupiah(pnbp_p_peg - pnbp_r_peg);

    let pnbp_p_brg = parseAngka(document.getElementById('pnbp_pagu_barang').value);
    let pnbp_r_brg = parseAngka(document.getElementById('pnbp_realisasi_barang').value);
    document.getElementById('pnbp_sisa_barang').value = formatRupiah(pnbp_p_brg - pnbp_r_brg);

    let pnbp_p_mod = parseAngka(document.getElementById('pnbp_pagu_modal').value);
    let pnbp_r_mod = parseAngka(document.getElementById('pnbp_realisasi_modal').value);
    document.getElementById('pnbp_sisa_modal').value = formatRupiah(pnbp_p_mod - pnbp_r_mod);

    let pnbp_sub_p = pnbp_p_peg + pnbp_p_brg + pnbp_p_mod;
    let pnbp_sub_r = pnbp_r_peg + pnbp_r_brg + pnbp_r_mod;
    document.getElementById('pnbp_sub_pagu').value = formatRupiah(pnbp_sub_p);
    document.getElementById('pnbp_sub_realisasi').value = formatRupiah(pnbp_sub_r);
    document.getElementById('pnbp_sub_sisa').value = formatRupiah(pnbp_sub_p - pnbp_sub_r);

    document.getElementById('grand_pagu').value = formatRupiah(rm_sub_p + pnbp_sub_p);
    document.getElementById('grand_realisasi').value = formatRupiah(rm_sub_r + pnbp_sub_r);
    document.getElementById('grand_sisa').value = formatRupiah((rm_sub_p - rm_sub_r) + (pnbp_sub_p - pnbp_sub_r));
}
</script>

<?php include 'layouts/footer.php'; ?>