<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Buat Pengajuan Baru</h2>
        <p>Isi formulir di bawah ini dengan lengkap untuk mengajukan inventaris.</p>
    </div>
    <a href="daftar_pengajuan.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama">
    <form action="proses_pengajuan.php" method="POST" enctype="multipart/form-data">
        
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Informasi Surat</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Surat Pengajuan</label>
                <input type="text" name="nama_surat" required>
            </div>
            <div class="form-group">
                <label>Tanggal Pengajuan</label>
                <input type="date" name="tanggal" required>
            </div>
            <div class="form-group">
                <label>Seksi / Subseksi</label>
                <select name="seksi" required>
                    <option value="" disabled selected>-- Pilih Seksi / Subseksi --</option>
                    
                    <option value="Tata usaha">Tata usaha</option>
                    <option value="Lantaskim">Lantaskim</option>
                    <option value="Intaltuskim">Intaltuskim</option>
                    <option value="Inteldakim">Inteldakim</option>
                    <option value="Tikkim">Tikkim</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama Pengaju</label>
                <input type="text" name="nama_pengaju" required>
            </div>

            <div class="form-group form-full">
                <label>Diketahui Oleh & Lampiran (JPG/PNG) <span style="color:#ff4c4c;">*</span></label>
                <div class="input-with-action">
                    
                    <select name="diketahui_oleh" required style="flex: 1;">
                        <option value="" disabled selected>-- Pilih Atasan --</option>
                        <option value="Drs. Ahmad Fauzi, M.Si">Drs. Ahmad Fauzi, M.Si (Kasi IT)</option>
                        <option value="Ir. Sri Wahyuni, M.M">Ir. Sri Wahyuni, M.M (Kabag Umum)</option>
                    </select>
                    
                    <div class="file-upload-container">
                        <div class="file-info" id="file-info">
                            <span id="file-name-text" class="file-name"></span>
                            <button type="button" id="btn-discard" class="btn-discard" title="Batal Upload">✖</button>
                        </div>

                        <div class="file-input-wrapper">
                            <button type="button" class="btn-file">📸 Pilih File</button>
                            <input type="file" name="lampiran" id="lampiran" accept="image/png, image/jpeg" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group form-full">
                <label>Keperluan</label>
                <textarea name="keperluan" rows="4" required placeholder="Tuliskan tujuan pengadaan barang..."></textarea>
            </div>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Daftar Barang</h3>
        
        <div id="container-barang">
            <div class="dynamic-row">
                <div class="form-group col-nama">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang[]" placeholder="ex: Laptop" required>
                </div>
                <div class="form-group col-jml">
                    <label>Jml</label>
                    <input type="number" name="jumlah[]" placeholder="0" required>
                </div>
                <div class="form-group col-sat">
                    <label>Satuan</label>
                    <input type="text" name="satuan[]" placeholder="ex: Unit" required>
                </div>
                <div class="form-group col-ket">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan[]" placeholder="ex: Kebutuhan lapangan">
                </div>
                <div class="col-aksi" style="width: 50px;"></div>
            </div>
        </div>

        <button type="button" id="btn-tambah" class="btn-tambah-row">+ Tambah Baris Barang</button>
        
        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">Ajukan Sekarang</button>
        </div>

    </form>
</div>

<?php include 'layouts/footer.php'; ?>

<script>
    document.getElementById('btn-tambah').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'dynamic-row';
        div.innerHTML = `
            <div class="form-group col-nama">
                <label style="opacity: 0.3;">Nama Barang</label>
                <input type="text" name="nama_barang[]" placeholder="Nama Barang" required>
            </div>
            <div class="form-group col-jml">
                <label style="opacity: 0.3;">Jml</label>
                <input type="number" name="jumlah[]" placeholder="0" required>
            </div>
            <div class="form-group col-sat">
                <label style="opacity: 0.3;">Satuan</label>
                <input type="text" name="satuan[]" placeholder="Satuan" required>
            </div>
            <div class="form-group col-ket">
                <label style="opacity: 0.3;">Keterangan</label>
                <input type="text" name="keterangan[]" placeholder="Keterangan">
            </div>
            <div class="col-aksi">
                <button type="button" class="btn-hapus" onclick="this.parentElement.parentElement.remove()" title="Hapus Baris">✖</button>
            </div>
        `;
        document.getElementById('container-barang').appendChild(div);
    });

    // --- LOGIC FILE UPLOAD  ---
    const fileInput = document.getElementById('lampiran');
    const fileInfo = document.getElementById('file-info');
    const fileNameText = document.getElementById('file-name-text');
    const btnDiscard = document.getElementById('btn-discard');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileNameText.textContent = this.files[0].name;
            fileInfo.style.display = 'flex';
        }
    });

    btnDiscard.addEventListener('click', function() {
        fileInput.value = '';
        fileInfo.style.display = 'none'; 
    });
</script>