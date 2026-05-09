<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'] ?? '';
if (!$id) die("ID tidak ditemukan");

// Tarik Data Master
$stmt = $pdo->prepare("SELECT * FROM pengajuan_inventaris WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) die("Data tidak ditemukan");

// Tarik Data Barang
$stmt_detail = $pdo->prepare("SELECT * FROM pengajuan_inventaris_detail WHERE id_pengajuan = ?");
$stmt_detail->execute([$id]);
$items = $stmt_detail->fetchAll();

// ==========================================
// 1. LOGIKA GAMBAR TANDA TANGAN (LAMPIRAN)
// ==========================================
$ttd_image_html = '';
$path_gambar = 'uploads/' . $data['lampiran'];

// Cek apakah file lampirannya beneran ada di folder uploads
if (!empty($data['lampiran']) && file_exists($path_gambar)) {
    $ttd_image_html = '<img src="' . $path_gambar . '" class="ttd-img">';
} else {
    // Kalau nggak ada TTD, kasih jarak kosong aja
    $ttd_image_html = '<div style="margin-top: 40px; color: #ccc; font-style: italic; font-size: 10px;">(Tanda Tangan Belum Diunggah)</div>';
}

// ==========================================
// 2. LOGIKA STEMPEL (NUMPUK DI ATAS TTD)
// ==========================================
$status_db = strtolower(trim($data['status']));
$stempel_html = '';

if (in_array($status_db, ['approve', 'approved', 'diterima', 'terima', 'acc', 'disetujui', 'selesai'])) {
    $stempel_html = '<div class="stamp-approved">APPROVED</div>';
} elseif (in_array($status_db, ['reject', 'rejected', 'ditolak', 'tolak'])) {
    $stempel_html = '<div class="stamp-rejected">REJECTED</div>';
} else {
    $stempel_html = '<div style="margin-top: 25px; color: #999; font-style: italic; font-size: 11px;">(Menunggu ACC)</div>';
}

$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "Arial", sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header-title { text-align: center; margin-top: 20px; }
        .header-title h1 { margin: 0; font-size: 18px; text-decoration: underline; }
        
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

        .ttd-section { width: 100%; margin-top: 30px; }
        .ttd-box { width: 40%; float: right; text-align: center; } 
        
        /* WADAH TTD & STEMPEL */
        .ttd-card { 
            height: 90px; 
            margin: 5px auto; 
            width: 100%; 
            display: block;
            position: relative; /* Kunci biar stempel bisa numpuk di dalam kotak ini */
            text-align: center;
        }
        
        /* GAMBAR TTD (BACKGROUND) */
        .ttd-img { 
            max-height: 80px; 
            max-width: 100%; 
            position: relative;
            z-index: 1; /* Posisi di bawah stempel */
            margin-top: 5px;
        }

        .ttd-name { font-weight: bold; border-bottom: 1px solid #000; display: inline-block; margin-top: 5px; min-width: 150px; }
        .clear { clear: both; }
        
        /* STEMPEL (FOREGROUND TINTA TRANSPARAN) */
        .stamp-approved {
            position: absolute;
            top: 20px; /* Atur naik turunnya stempel */
            left: 20%; /* Atur geser kiri kanannya */
            border: 3px solid rgba(34, 139, 34, 0.6); /* Warna hijau transparan ala tinta cap */
            color: rgba(34, 139, 34, 0.6); 
            font-weight: bold; 
            padding: 8px 15px;
            display: inline-block; 
            transform: rotate(-15deg);
            font-size: 20px; 
            letter-spacing: 2px;
            border-radius: 5px;
            z-index: 10; /* Posisi di atas gambar TTD */
        }
        
        .stamp-rejected {
            position: absolute;
            top: 20px;
            left: 20%;
            border: 3px solid rgba(220, 20, 60, 0.6); /* Warna merah transparan */
            color: rgba(220, 20, 60, 0.6); 
            font-weight: bold; 
            padding: 8px 15px;
            display: inline-block; 
            transform: rotate(-15deg);
            font-size: 20px; 
            letter-spacing: 2px;
            border-radius: 5px;
            z-index: 10;
        }
    </style>
</head>
<body>

    <div class="header-title">
        <h1>FORMULIR PENGAJUAN INVENTARIS BARANG</h1>
    </div>

    <table class="info-section">
        <tr>
            <td width="15%">Tanggal Pengajuan</td><td width="2%">:</td>
            <td width="83%"><span class="line-bottom">' . date('d F Y', strtotime($data['tanggal'])) . '</span></td>
        </tr>
        <tr>
            <td>Seksi / Subseksi</td><td>:</td>
            <td><span class="line-bottom">' . htmlspecialchars($data['seksi']) . '</span></td>
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
                <td>' . htmlspecialchars($item['nama_barang']) . '</td>
                <td class="text-center" style="color: blue;">' . htmlspecialchars($item['jumlah']) . '</td>
                <td class="text-center">' . htmlspecialchars($item['satuan']) . '</td>
                <td class="text-italic">' . htmlspecialchars($item['keterangan']) . '</td>
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
            Tanjungpinang, ' . date('d F Y', strtotime($data['tanggal'])) . '<br>
            Mengetahui / Menyetujui,<br>
            <div class="ttd-card">
                ' . $ttd_image_html . ' ' . $stempel_html . ' </div>
            <span class="ttd-name">' . htmlspecialchars($data['diketahui_oleh']) . '</span><br>
            Pejabat Berwenang
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

$dompdf->stream("Form_Pengajuan_Inventaris.pdf", array("Attachment" => 0)); 
?>