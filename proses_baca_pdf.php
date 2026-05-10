<?php
// Manggil mesin PDF Parser
require 'vendor/autoload.php';

header('Content-Type: application/json');

if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
    $file_path = $_FILES['file_pdf']['tmp_name'];

    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($file_path);
        
        // Sedot semua teks dari PDF
        $text = $pdf->getText();

        // -------------------------------------------------------------
        // FUNGSI MATA ELANG (MEMBELAH ANGKA DEMPET)
        // -------------------------------------------------------------
        function tangkapPaguRealisasi($zona_teks) {
            // Pecah teks jadi per baris
            $lines = explode("\n", $zona_teks);
            $pagu = [];
            $realisasi = [];

            foreach ($lines as $line) {
                // Rumus sakti: Tangkap semua angka berformat Rp (misal: 1.000.000) atau angka 0 tunggal
                // Kalau angkanya dempet "1.0002.000", rumus ini otomatis motong jadi "1.000" dan "2.000"
                if (preg_match_all('/\d{1,3}(?:\.\d{3})+|\b0\b/', $line, $matches)) {
                    
                    // Kalau dalam 1 baris ada minimal 3 angka berderet, berarti ini baris target!
                    if (count($matches[0]) >= 3) {
                        if (empty($pagu)) {
                            $pagu = $matches[0]; // Baris pertama yang ketemu = PAGU
                        } elseif (empty($realisasi)) {
                            $realisasi = $matches[0]; // Baris kedua yang ketemu = REALISASI
                            break; // Selesai nyari!
                        }
                    }
                }
            }

            return [
                'peg_p' => $pagu[0] ?? 0, 'brg_p' => $pagu[1] ?? 0, 'mod_p' => $pagu[2] ?? 0,
                'peg_r' => $realisasi[0] ?? 0, 'brg_r' => $realisasi[1] ?? 0, 'mod_r' => $realisasi[2] ?? 0,
            ];
        }

        // Cek PDF apa yang diupload user?
        $is_sumber_dana = preg_match('/RUPIAH MURNI/i', $text);

        if ($is_sumber_dana) {
            // ============================================
            // JIKA USER UPLOAD PDF SUMBER DANA
            // ============================================
            preg_match('/RUPIAH MURNI(.*?)(?=PENERIMAAN NEGARA BUKAN PAJAK|$)/is', $text, $match_rm);
            $rm = tangkapPaguRealisasi($match_rm[1] ?? '');

            preg_match('/PENERIMAAN NEGARA BUKAN PAJAK(.*?)SISA/is', $text, $match_pnbp);
            $pnbp = tangkapPaguRealisasi($match_pnbp[1] ?? '');

            echo json_encode([
                'status' => 'success',
                'rm_p_peg' => $rm['peg_p'], 'rm_p_brg' => $rm['brg_p'], 'rm_p_mod' => $rm['mod_p'],
                'rm_r_peg' => $rm['peg_r'], 'rm_r_brg' => $rm['brg_r'], 'rm_r_mod' => $rm['mod_r'],
                'pnbp_p_peg' => $pnbp['peg_p'], 'pnbp_p_brg' => $pnbp['brg_p'], 'pnbp_p_mod' => $pnbp['mod_p'],
                'pnbp_r_peg' => $pnbp['peg_r'], 'pnbp_r_brg' => $pnbp['brg_r'], 'pnbp_r_mod' => $pnbp['mod_r'],
            ]);

        } else {
            // ============================================
            // JIKA USER (TIDAK SENGAJA) UPLOAD PDF JENIS BELANJA
            // ============================================
            $jb = tangkapPaguRealisasi($text);

            echo json_encode([
                'status' => 'success',
                // Kita masukin datanya ke kolom (A) Rupiah Murni aja biar nggak error
                'rm_p_peg' => $jb['peg_p'], 'rm_p_brg' => $jb['brg_p'], 'rm_p_mod' => $jb['mod_p'],
                'rm_r_peg' => $jb['peg_r'], 'rm_r_brg' => $jb['brg_r'], 'rm_r_mod' => $jb['mod_r'],
                // PNBP dikosongin
                'pnbp_p_peg' => 0, 'pnbp_p_brg' => 0, 'pnbp_p_mod' => 0,
                'pnbp_r_peg' => 0, 'pnbp_r_brg' => 0, 'pnbp_r_mod' => 0,
            ]);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal memproses PDF. Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'File PDF tidak ditemukan atau rusak.']);
}
?>