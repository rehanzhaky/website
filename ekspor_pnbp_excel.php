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

// Query data
$sql = "SELECT m.*, 
               (SELECT SUM(estimasi) FROM laporan_pnbp_detail d WHERE d.id_laporan = m.id) AS grand_target,
               (SELECT SUM(realisasi) FROM laporan_pnbp_detail d WHERE d.id_laporan = m.id) AS grand_realisasi
        FROM laporan_pnbp m 
        WHERE m.tanggal_laporan BETWEEN :mulai AND :sampai 
        ORDER BY m.tanggal_laporan DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['mulai' => $mulai_tanggal, 'sampai' => $sampai_tanggal]);
$data = $stmt->fetchAll();

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'LAPORAN PNBP (PENERIMAAN NEGARA BUKAN PAJAK)');
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($mulai_tanggal)) . ' s.d. ' . date('d/m/Y', strtotime($sampai_tanggal)));
$sheet->mergeCells('A2:F2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Table Header
$row = 4;
$sheet->setCellValue('A' . $row, 'NO');
$sheet->setCellValue('B' . $row, 'TGL. LAPORAN');
$sheet->setCellValue('C' . $row, 'KETERANGAN');
$sheet->setCellValue('D' . $row, 'TOTAL TARGET');
$sheet->setCellValue('E' . $row, 'TOTAL REALISASI');
$sheet->setCellValue('F' . $row, 'PERSENTASE');

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
foreach ($data as $item) {
    $target = $item['grand_target'] ?: 0;
    $realisasi = $item['grand_realisasi'] ?: 0;
    $persen = $target > 0 ? ($realisasi / $target) * 100 : 0;
    
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal_laporan'])));
    $sheet->setCellValue('C' . $row, $item['keterangan']);
    $sheet->setCellValue('D' . $row, $target);
    $sheet->setCellValue('E' . $row, $realisasi);
    $sheet->setCellValue('F' . $row, number_format($persen, 2) . '%');
    
    // Format currency
    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    // Borders
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    $row++;
}

// Auto width
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download
$filename = 'Laporan_PNBP_' . date('Ymd') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
