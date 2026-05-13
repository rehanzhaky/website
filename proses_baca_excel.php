<?php
// MATIKAN SEMUA DISPLAY ERROR BIAR GAK RUSAK JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start(); 

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json; charset=utf-8');

if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] == 0) {
    try {
        $file = $_FILES['file_excel']['tmp_name'];
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(); 

        $data = [];
        foreach ($rows as $row) {
            // Kolom A = Kode (Index 0), Kolom C = Pagu (Index 2), Kolom G = Realisasi (Index 6)
            $kodeRaw = trim($row[0] ?? '');
            if (empty($kodeRaw)) continue;

            $kodeKey = strtoupper(str_replace('.', ' ', $kodeRaw));
            $pagu = preg_replace('/[^\d]/', '', $row[2] ?? '0');
            $real = preg_replace('/[^\d]/', '', $row[6] ?? '0');

            if (is_numeric($pagu) && $pagu > 0) {
                $data[$kodeKey] = [
                    'pagu' => (int)$pagu,
                    'real' => (int)$real
                ];
            }
        }

        ob_clean(); // Hapus sisa-sisa teks/warning/html nyasar
        echo json_encode(['status' => 'success', 'data' => $data]);
    } catch (Exception $e) {
        ob_clean();
        echo json_encode(['status' => 'error', 'pesan' => $e->getMessage()]);
    }
} else {
    ob_clean();
    echo json_encode(['status' => 'error', 'pesan' => 'File tidak terdeteksi.']);
}
exit;