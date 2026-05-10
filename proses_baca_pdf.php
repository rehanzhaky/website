<?php
require 'vendor/autoload.php';
header('Content-Type: application/json');

if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
    $file_path = $_FILES['file_pdf']['tmp_name'];

    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($file_path);
        $text = $pdf->getText();

        // ----------------------------------------------------------------
        // 1. STRATEGI MATA ELANG UNTUK PNBP (BACA KOLOM LDR)
        // ----------------------------------------------------------------
        $is_pnbp = preg_match('/Realisasi PNBP Per Jenis Akun/i', $text);
        
        if ($is_pnbp) {
            
            // TANGKAP TARGET (Di blok atas)
            function getTarget($text, $current_code, $next_code) {
                $pattern = $next_code ? '/'.$current_code.'(.*?)'.$next_code.'/is' : '/'.$current_code.'(.*?)(LAPORAN|TOTAL|Realisasi)/is';
                if (preg_match($pattern, $text, $match)) {
                    // Cari angka dengan titik (misal 1.000) atau 0
                    if (preg_match_all('/\b\d{1,3}(?:\.\d{3})+\b|\b0\b/', $match[1], $nums)) {
                        return end($nums[0]); // Ambil angka paling terakhir sebelum kode berikutnya
                    }
                }
                return 0;
            }

            $t_131 = getTarget($text, '425131', '425151');
            $t_151 = getTarget($text, '425151', '425211');
            $t_211 = getTarget($text, '425211', '425212');
            $t_212 = getTarget($text, '425212', '425213');
            $t_213 = getTarget($text, '425213', '425214');
            $t_214 = getTarget($text, '425214', null);

            // TANGKAP REALISASI (Di blok paling bawah)
            $r_131 = 0; $r_151 = 0; $r_211 = 0; $r_212 = 0; $r_213 = 0; $r_214 = 0;
            if (preg_match('/Realisasi Satker(?:Realisasi SPAN)?(.*)/is', $text, $block)) {
                $lines = explode("\n", trim($block[1]));
                $realisasi_arr = [];
                
                foreach ($lines as $line) {
                    // Cek kalau barisnya mengandung angka
                    if (preg_match_all('/\b\d{1,3}(?:\.\d{3})+\b|\b0\b/', $line, $nums)) {
                        $realisasi_arr[] = $nums[0][0]; // Ambil angka pertama aja (Realisasi Satker), SPAN dicuekin
                    }
                    if (count($realisasi_arr) >= 6) break; // Cuma butuh 6 baris akun
                }
                
                $r_131 = $realisasi_arr[0] ?? 0;
                $r_151 = $realisasi_arr[1] ?? 0;
                $r_211 = $realisasi_arr[2] ?? 0;
                $r_212 = $realisasi_arr[3] ?? 0;
                $r_213 = $realisasi_arr[4] ?? 0;
                $r_214 = $realisasi_arr[5] ?? 0;
            }

            echo json_encode([
                'status' => 'success',
                'tipe_pdf' => 'pnbp_akun',
                't_131' => $t_131, 'r_131' => $r_131,
                't_151' => $t_151, 'r_151' => $r_151,
                't_211' => $t_211, 'r_211' => $r_211,
                't_212' => $t_212, 'r_212' => $r_212,
                't_213' => $t_213, 'r_213' => $r_213,
                't_214' => $t_214, 'r_214' => $r_214
            ]);
            exit;
        }

        // ----------------------------------------------------------------
        // 2. STRATEGI UNTUK REALISASI ANGGARAN (Sumber Dana / Jenis Belanja)
        // ----------------------------------------------------------------
        function tangkapPaguRealisasi($zona_teks) {
            $lines = explode("\n", $zona_teks);
            $pagu = []; $realisasi = [];

            foreach ($lines as $line) {
                if (preg_match_all('/\d{1,3}(?:\.\d{3})+|\b0\b/', $line, $matches)) {
                    if (count($matches[0]) >= 3) {
                        if (empty($pagu)) {
                            $pagu = $matches[0]; 
                        } elseif (empty($realisasi)) {
                            $realisasi = $matches[0]; 
                            break; 
                        }
                    }
                }
            }
            return [
                'peg_p' => $pagu[0] ?? 0, 'brg_p' => $pagu[1] ?? 0, 'mod_p' => $pagu[2] ?? 0,
                'peg_r' => $realisasi[0] ?? 0, 'brg_r' => $realisasi[1] ?? 0, 'mod_r' => $realisasi[2] ?? 0,
            ];
        }

        $is_sumber_dana = preg_match('/RUPIAH MURNI/i', $text);

        if ($is_sumber_dana) {
            preg_match('/RUPIAH MURNI(.*?)(?=PENERIMAAN NEGARA BUKAN PAJAK|$)/is', $text, $match_rm);
            $rm = tangkapPaguRealisasi($match_rm[1] ?? '');

            preg_match('/PENERIMAAN NEGARA BUKAN PAJAK(.*?)SISA/is', $text, $match_pnbp);
            $pnbp = tangkapPaguRealisasi($match_pnbp[1] ?? '');

            echo json_encode([
                'status' => 'success',
                'tipe_pdf' => 'sumber_dana',
                'rm_p_peg' => $rm['peg_p'], 'rm_p_brg' => $rm['brg_p'], 'rm_p_mod' => $rm['mod_p'],
                'rm_r_peg' => $rm['peg_r'], 'rm_r_brg' => $rm['brg_r'], 'rm_r_mod' => $rm['mod_r'],
                'pnbp_p_peg' => $pnbp['peg_p'], 'pnbp_p_brg' => $pnbp['brg_p'], 'pnbp_p_mod' => $pnbp['mod_p'],
                'pnbp_r_peg' => $pnbp['peg_r'], 'pnbp_r_brg' => $pnbp['brg_r'], 'pnbp_r_mod' => $pnbp['mod_r'],
            ]);
        } else {
            $jb = tangkapPaguRealisasi($text);
            echo json_encode([
                'status' => 'success',
                'tipe_pdf' => 'jenis_belanja',
                'rm_p_peg' => $jb['peg_p'], 'rm_p_brg' => $jb['brg_p'], 'rm_p_mod' => $jb['mod_p'],
                'rm_r_peg' => $jb['peg_r'], 'rm_r_brg' => $jb['brg_r'], 'rm_r_mod' => $jb['mod_r'],
            ]);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal memproses PDF. Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'File PDF tidak ditemukan atau rusak.']);
}
?>