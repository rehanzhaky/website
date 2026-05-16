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

$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 
$filter_seksi   = $_GET['seksi'] ?? '';

// Query data
$sql = "SELECT m.*, 
               (SELECT SUM(realisasi) FROM laporan_realisasi_bidang_detail d WHERE d.id_laporan = m.id) AS grand_realisasi
        FROM laporan_realisasi_bidang m 
        WHERE m.tanggal_laporan BETWEEN :mulai AND :sampai ";

$params = ['mulai' => $mulai_tanggal, 'sampai' => $sampai_tanggal];

if (!empty($filter_seksi)) {
    $sql .= " AND m.seksi = :seksi ";
    $params['seksi'] = $filter_seksi;
}

$sql .= " ORDER BY m.tanggal_laporan DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'LAPORAN REALISASI ANGGARAN PER BIDANG');
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$periode_text = 'Periode: ' . date('d/m/Y', strtotime($mulai_tanggal)) . ' s.d. ' . date('d/m/Y', strtotime($sampai_tanggal));
if (!empty($filter_seksi)) {
    $periode_text .= ' | Seksi: ' . $filter_seksi;
}
$sheet->setCellValue('A2', $periode_text);
$sheet->mergeCells('A2:F2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Table Header
$row = 4;
$sheet->setCellValue('A' . $row, 'NO');
$sheet->setCellValue('B' . $row, 'TGL. LAPORAN');
$sheet->setCellValue('C' . $row, 'SEKSI / BIDANG');
$sheet->setCellValue('D' . $row, 'KETERANGAN');
$sheet->setCellValue('E' . $row, 'TOTAL REALISASI');

// Style header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($headerStyle);

// Data rows
$row++;
$no = 1;
foreach ($data as $item) {
    $realisasi = $item['grand_realisasi'] ?: 0;
    
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal_laporan'])));
    $sheet->setCellValue('C' . $row, $item['seksi']);
    $sheet->setCellValue('D' . $row, $item['keterangan']);
    $sheet->setCellValue('E' . $row, $realisasi);
    
    // Format currency
    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    // Borders
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// Auto width
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download
$filename = 'Laporan_Realisasi_Bidang_' . date('Ymd') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
