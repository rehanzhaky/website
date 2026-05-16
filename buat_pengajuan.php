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
                <label>Seksi / Subseksi</label>
                <select name="seksi" required>
                    <option value="" disabled selected>-- Pilih Seksi / Subseksi --</option>
                    
                    <option value="Sub Bag Tata Usaha">Sub Bagian Tata Usaha (TU)</option>
                    <option value="Sub Bag Tata Usaha">Urusan Umum</option>
                    <option value="Sub Bag Tata Usaha">Urusan Kepegawaian</option>
                    <option value="Sub Bag Tata Usaha">Urusan Keuangan</option>
                    <option value="Lantaskim">Seksi Lalu Lintas Keimigrasian (Lantaskim)</option>
                    <option value="Intaltuskim">Seksi Izin Tinggal dan Status Keimigrasian (Intaltuskim)</option>
                    <option value="Inteldakim">Seksi Intelijen dan Penindakan Keimigrasian (Inteldakim)</option>
                    <option value="Tikim">Seksi Teknologi Informasi dan Komunikasi Keimigrasian (Tikkim)</option>
                    <option value="TPi">Tempat Pemeriksaan Imigrasi (TPI)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal Pengajuan</label>
                <input type="date" name="tanggal" required>
            </div>

            <div class="form-group form-full">
                <label>Diketahui Oleh & Lampiran (JPG/PNG) <span style="color:#ff4c4c;">*</span></label>
                <div class="input-with-action">
                    
                    <select name="diketahui_oleh" required style="flex: 1;">
                        <option value="" disabled selected>-- Pilih Atasan --</option>
                        <option value="Robby Marteja, S.E">Robby Marteja, S.E</option>
                        <option value="Sri Deswita Undariyani, S.AP">Sri Deswita Undariyani, S.AP</option>
                        <option value="Faiza, S.IP">Faiza, S.IP</option>
                        <option value="Lia Andini, S.AP">Lia Andini, S.AP</option>
                        <option value="Kusmartono W., A.Md.Im., S.H. M.M">Kusmartono W., A.Md.Im., S.H. M.M</option>
                        <option value="Alexander Sianturi, S.H">Alexander Sianturi, S.H</option>
                        <option value="Made Hery Susanta, S.H">Made Hery Susanta, S.H</option>
                        <option value="Daniel Maxrinto, S.H">Daniel Maxrinto, S.H</option>
                        <option value="Gandhi Agung Wibowo, S.E">Gandhi Agung Wibowo, S.E</option>
                        <option value="Fikky Amirullah, S.Tr.Im">Fikky Amirullah, S.Tr.Im</option>
                        <option value="Baharuddin, S.H">Baharuddin, S.H</option>
                        <option value="Herlis Fahmil Qur'ani, S.Tr.Im">Herlis Fahmil Qur'ani, S.Tr.Im</option>
                        <option value="Yongki Sepriance, S.H">Yongki Sepriance, S.H</option>
                        <option value="Muhammad Denny Ridwan, A.Md.Im., S.H">Muhammad Denny Ridwan, A.Md.Im., S.H</option>
                        <option value="Edwin Budi Santika, S.Tr.Im">Edwin Budi Santika, S.Tr.Im</option>
                        <option value="Sandy Rifki Enricko, S.H">Sandy Rifki Enricko, S.H</option>
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