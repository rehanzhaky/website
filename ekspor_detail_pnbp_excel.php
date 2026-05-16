<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit; }
require_once 'config/koneksi.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$id_laporan = $_GET['id'] ?? null;

if (!$id_laporan) {
    header("Location: laporan_pnbp.php");
    exit;
}

// 1. Ambil Data Master PNBP
$stmt_master = $pdo->prepare("SELECT * FROM laporan_pnbp WHERE id = ?");
$stmt_master->execute([$id_laporan]);
$laporan = $stmt_master->fetch();

if (!$laporan) {
    header("Location: laporan_pnbp.php");
    exit;
}

// 2. Ambil Data Detail PNBP
$stmt_detail = $pdo->prepare("SELECT * FROM laporan_pnbp_detail WHERE id_laporan = ?");
$stmt_detail->execute([$id_laporan]);
$raw_details = $stmt_detail->fetchAll();

// Penggabungan Data (Sewa Tanah 425131 -> Sarpras 425151)
$details = [];
foreach ($raw_details as $row) {
    $kode = $row['kode_akun'];
    
    $estimasi = $row['estimasi'] ?? 0;
    $realisasi = $row['realisasi'] ?? 0;
    $sisa = $estimasi - $realisasi;
    
    if ($kode == '425131') {
        if (!isset($details['425151'])) {
            $details['425151'] = [
                'kode_akun' => '425151',
                'nama_akun' => 'Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi',
                'estimasi' => 0, 'realisasi' => 0, 'sisa' => 0
            ];
        }
        $details['425151']['estimasi'] += $estimasi;
        $details['425151']['realisasi'] += $realisasi;
        $details['425151']['sisa'] += $sisa;
    } else {
        if (!isset($details[$kode])) {
            $details[$kode] = $row;
            $details[$kode]['estimasi'] = $estimasi;
            $details[$kode]['sisa'] = $sisa;
        } else {
            $details[$kode]['estimasi'] += $estimasi;
            $details[$kode]['realisasi'] += $realisasi;
            $details[$kode]['sisa'] += $sisa;
        }
    }
}

// Kalkulasi Total
$tot_target = 0;
$tot_realisasi = 0;
$tot_sisa = 0;
foreach ($details as $d) {
    $tot_target += $d['estimasi'];
    $tot_realisasi += $d['realisasi'];
    $tot_sisa += $d['sisa'];
}
$tot_persen = $tot_target > 0 ? ($tot_realisasi / $tot_target) * 100 : 0;

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Info
$sheet->setCellValue('A1', 'DETAIL LAPORAN PNBP');
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Keterangan: ' . $laporan['keterangan']);
$sheet->mergeCells('A2:F2');
$sheet->setCellValue('A3', 'Tanggal Laporan: ' . date('d F Y', strtotime($laporan['tanggal_laporan'])));
$sheet->mergeCells('A3:F3');

// Table Header
$row = 5;
$sheet->setCellValue('A' . $row, 'NO');
$sheet->setCellValue('B' . $row, 'KODE AKUN');
$sheet->setCellValue('C' . $row, 'NAMA AKUN');
$sheet->setCellValue('D' . $row, 'ESTIMASI');
$sheet->setCellValue('E' . $row, 'REALISASI');
$sheet->setCellValue('F' . $row, 'SISA');

// Style header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($headerStyle);

// Data rows
$row++;
$no = 1;
foreach ($details as $item) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $item['kode_akun']);
    $sheet->setCellValue('C' . $row, $item['nama_akun']);
    $sheet->setCellValue('D' . $row, $item['estimasi']);
    $sheet->setCellValue('E' . $row, $item['realisasi']);
    $sheet->setCellValue('F' . $row, $item['sisa']);
    
    // Format currency
    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    // Borders
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// Total row
$sheet->setCellValue('A' . $row, 'TOTAL');
$sheet->mergeCells('A' . $row . ':C' . $row);
$sheet->setCellValue('D' . $row, $tot_target);
$sheet->setCellValue('E' . $row, $tot_realisasi);
$sheet->setCellValue('F' . $row, $tot_sisa);

// Style total
$totalStyle = [
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($totalStyle);
$sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

// Persentase row
$row++;
$sheet->setCellValue('A' . $row, 'PERSENTASE REALISASI');
$sheet->mergeCells('A' . $row . ':C' . $row);
$sheet->setCellValue('D' . $row, number_format($tot_persen, 2) . '%');
$sheet->mergeCells('D' . $row . ':F' . $row);
$sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($totalStyle);

// Auto width
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download
$filename = 'Detail_PNBP_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
