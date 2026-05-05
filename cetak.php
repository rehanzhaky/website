<?php
require_once 'config/koneksi.php';
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'] ?? '';
if (!$id) die("ID tidak ditemukan");

// Ambil Data Master & Detail
$stmt = $pdo->prepare("SELECT * FROM pengajuan_inventaris WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

$stmt_detail = $pdo->prepare("SELECT * FROM pengajuan_inventaris_detail WHERE id_pengajuan = ?");
$stmt_detail->execute([$id]);
$items = $stmt_detail->fetchAll();

if (!$data) die("Data tidak ditemukan");

// LOGIKA STEMPEL DINAMIS BERDASARKAN STATUS
$status_db = strtolower($data['status']);
$stempel_html = '';

if ($status_db == 'approve' || $status_db == 'diterima') {
    $stempel_html = '<div class="stamp-approved">APPROVED</div>';
} elseif ($status_db == 'reject' || $status_db == 'ditolak') {
    $stempel_html = '<div class="stamp-rejected">REJECTED</div>';
} else {
    $stempel_html = '<div style="margin-top: 25px; color: #999; font-style: italic;">(Menunggu ACC)</div>';
}

$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "Arial", sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header-title { text-align: center; margin-top: 20px; }
        .header-title h1 { margin: 0; font-size: 18px; text-decoration: underline; }
        .header-title p { margin: 2px 0; font-size: 11px; color: #555; }

        .info-section { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .info-section td { padding: 5px 2px; vertical-align: bottom; }
        .line-bottom { border-bottom: 1px solid #333; min-width: 150px; display: inline-block; }
        
        .tabel-barang { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .tabel-barang th { background-color: #e9f2f9; color: #333; border: 1px solid #999; padding: 8px; text-align: center; }
        .tabel-barang td { border: 1px solid #999; padding: 6px 8px; }
        .text-center { text-align: center; }
        .text-italic { font-style: italic; color: #444; }

        .note { margin-top: 15px; font-size: 10px; display: flex; align-items: center; }
        .checkbox { width: 12px; height: 12px; border: 1px solid #000; display: inline-block; background: #2e5a88; margin-right: 5px; }

        .ttd-section { width: 100%; margin-top: 30px; text-align: center; }
        .ttd-box { width: 32%; float: left; }
        .ttd-card { 
            border: 1px dashed #999; 
            background: #f9f9f9; 
            height: 80px; 
            margin: 10px auto; 
            width: 90%; 
            display: block;
            position: relative;
        }
        .ttd-img { max-height: 60px; max-width: 100%; margin-top: 10px; }
        .ttd-name { font-weight: bold; border-bottom: 1px solid #000; display: inline-block; margin-top: 5px; }
        .clear { clear: both; }
        
        .stamp-approved {
            border: 2px solid green; color: green; font-weight: bold; padding: 5px;
            display: inline-block; margin-top: 20px; transform: rotate(-5deg);
        }
        
        /* TAMBAHAN CSS BUAT STEMPEL DITOLAK */
        .stamp-rejected {
            border: 2px solid red; color: red; font-weight: bold; padding: 5px;
            display: inline-block; margin-top: 20px; transform: rotate(-5deg);
        }
    </style>
</head>
<body>

    <div class="header-title">
        <h1>FORMULIR PENGAJUAN INVENTARIS BARANG</h1>
        <p>Nomor: ' . $data['nomor_surat'] . '</p>
    </div>

    <table class="info-section">
        <tr>
            <td width="15%">Tanggal Pengajuan</td><td width="2%">:</td>
            <td width="33%"><span class="line-bottom">' . date('d F Y', strtotime($data['tanggal'])) . '</span></td>
            <td width="15%">No. Referensi</td><td width="2%">:</td>
            <td width="33%"><span class="line-bottom">' . $data['nomor_surat'] . '</span></td>
        </tr>
        <tr>
            <td>Seksi / Divisi</td><td>:</td>
            <td><span class="line-bottom">' . $data['seksi'] . '</span></td>
            <td>Tahun Anggaran</td><td>:</td>
            <td><span class="line-bottom">' . date('Y', strtotime($data['tanggal'])) . '</span></td>
        </tr>
        <tr>
            <td>Nama Pengaju</td><td>:</td>
            <td><span class="line-bottom">' . $data['nama_pengaju'] . '</span></td>
            <td>Jabatan</td><td>:</td>
            <td><span class="line-bottom">Staf / Fungsional</span></td>
        </tr>
        <tr>
            <td>Keperluan</td><td>:</td>
            <td colspan="4"><span class="line-bottom" style="width: 95%;">' . $data['keperluan'] . '</span></td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-top: 20px;">Daftar Barang yang Diajukan:</div>
    <table class="tabel-barang">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="35%">Jenis Barang</th>
                <th width="10%">Jumlah</th>
                <th width="15%">Satuan</th>
                <th width="35%">Keterangan</th>
            </tr>
        </thead>
        <tbody>';
        
        $no = 1;
        foreach($items as $item) {
            $html .= '<tr>
                <td class="text-center">' . $no++ . '</td>
                <td>' . $item['nama_barang'] . '</td>
                <td class="text-center" style="color: blue;">' . $item['jumlah'] . '</td>
                <td class="text-center">' . $item['satuan'] . '</td>
                <td class="text-italic">' . $item['keterangan'] . '</td>
            </tr>';
        }

$html .= '
        </tbody>
    </table>

    <div class="note">
        <div class="checkbox"></div> Barang yang diajukan merupakan kebutuhan nyata dan mendesak untuk mendukung kegiatan operasional unit kerja.
    </div>

    <div style="font-weight: bold; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px;">Tanda Tangan dan Pengesahan:</div>
    
    <div class="ttd-section">
        <div class="ttd-box">
            Diajukan Oleh,<br>
            <div class="ttd-card"></div>
            <span class="ttd-name">' . $data['nama_pengaju'] . '</span><br>
            Staf Pengaju
        </div>
        
        <div class="ttd-box">
            Diketahui Oleh,<br>
            <div class="ttd-card">
                <img src="uploads/' . $data['lampiran'] . '" class="ttd-img">
            </div>
            <span class="ttd-name">' . $data['diketahui_oleh'] . '</span><br>
            Atasan Terkait
        </div>

        <div class="ttd-box">
            Menyetujui,<br>
            <div class="ttd-card">
                <!-- INI DIA MAGIC-NYA: Variabel yang nampilin stempel sesuai status -->
                ' . $stempel_html . '
            </div>
            <span class="ttd-name">Ir. Sri Wahyuni, M.M</span><br>
            Kepala Bagian Umum
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('chroot', __DIR__);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Form_Pengajuan_" . $id . ".pdf", array("Attachment" => 1));
?>