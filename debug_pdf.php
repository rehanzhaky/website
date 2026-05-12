<?php
require 'vendor/autoload.php';
header('Content-Type: application/json');

if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($_FILES['file_pdf']['tmp_name']);
        
        echo json_encode([
            'status' => 'success',
            'teks_mentah' => $pdf->getText()
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'pesan' => $e->getMessage()]);
    }
}
?>