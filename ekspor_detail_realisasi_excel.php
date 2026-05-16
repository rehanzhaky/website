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
    header("Location: realisasi_anggaran.php");
    exit;
}

// 1. Ambil Data Master
$stmt_master = $pdo->prepare("SELECT * FROM laporan_realisasi_bidang WHERE id = ?");
$stmt_master->execute([$id_laporan]);
$laporan = $stmt_master->fetch();

if (!$laporan) {
    header("Location: realisasi_anggaran.php");
    exit;
}

// 2. Ambil Data Detail
$stmt_detail = $pdo->prepare("SELECT * FROM laporan_realisasi_bidang_detail WHERE id_laporan = ?");
$stmt_detail->execute([$id_laporan]);
$details = $stmt_detail->fetchAll();

// 3. Kalkulasi Total
$tot_pagu = 0;
$tot_realisasi = 0;
$tot_sisa = 0;

foreach ($details as $row) {
    $tot_pagu += $row['pagu'];
    $tot_realisasi += $row['realisasi'];
    $tot_sisa += $row['sisa'];
}
$tot_persen = $tot_pagu > 0 ? ($tot_realisasi / $tot_pagu) * 100 : 0;

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Info
$sheet->setCellValue('A1', 'DETAIL REALISASI ANGGARAN PER BIDANG');
$sheet->mergeCells('A1:G1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Seksi/Bidang: ' . $laporan['seksi']);
$sheet->mergeCells('A2:G2');
$sheet->setCellValue('A3', 'Keterangan: ' . $laporan['keterangan']);
$sheet->mergeCells('A3:G3');
$sheet->setCellValue('A4', 'Tanggal Laporan: ' . date('d F Y', strtotime($laporan['tanggal_laporan'])));
$sheet->mergeCells('A4:G4');

// Table Header
$row = 6;
$sheet->setCellValue('A' . $row, 'NO');
$sheet->setCellValue('B' . $row, 'KODE');
$sheet->setCellValue('C' . $row, 'URAIAN KEGIATAN');
$sheet->setCellValue('D' . $row, 'PAGU');
$sheet->setCellValue('E' . $row, 'REALISASI');
$sheet->setCellValue('F' . $row, 'SISA');
$sheet->setCellValue('G' . $row, '%');

// Style header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($headerStyle);

// Data rows
$row++;
$no = 1;
foreach ($details as $item) {
    $persen = $item['pagu'] > 0 ? ($item['realisasi'] / $item['pagu']) * 100 : 0;
    
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $item['kode_akun']);
    $sheet->setCellValue('C' . $row, $item['uraian']);
    $sheet->setCellValue('D' . $row, $item['pagu']);
    $sheet->setCellValue('E' . $row, $item['realisasi']);
    $sheet->setCellValue('F' . $row, $item['sisa']);
    $sheet->setCellValue('G' . $row, number_format($persen, 2) . '%');
    
    // Format currency
    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    // Borders
    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// Total row
$sheet->setCellValue('A' . $row, 'TOTAL');
$sheet->mergeCells('A' . $row . ':C' . $row);
$sheet->setCellValue('D' . $row, $tot_pagu);
$sheet->setCellValue('E' . $row, $tot_realisasi);
$sheet->setCellValue('F' . $row, $tot_sisa);
$sheet->setCellValue('G' . $row, number_format($tot_persen, 2) . '%');

// Style total
$totalStyle = [
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($totalStyle);
$sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

// Auto width
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download
$filename = 'Detail_Realisasi_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
