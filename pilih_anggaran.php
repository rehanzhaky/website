<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';
include '.includes/header.php';
include '.includes/toast_notification.php';

$filter     = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date'])   ? $_GET['end_date']   : '';

$whereClause = "";

switch ($filter) {
    case 'today':
        $whereClause = "WHERE DATE(tanggal_pengambilan) = CURDATE()";
        break;
    case 'week':
        $whereClause = "WHERE YEARWEEK(tanggal_pengambilan, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $whereClause = "WHERE MONTH(tanggal_pengambilan) = MONTH(CURDATE()) AND YEAR(tanggal_pengambilan) = YEAR(CURDATE())";
        break;
    case 'custom':
        if (!empty($start_date) && !empty($end_date)) {
            $whereClause = "WHERE DATE(tanggal_pengambilan) BETWEEN '$start_date' AND '$end_date'";
        }
        break;
    default:
        $whereClause = ""; 
        break;
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0 p-0">Ekspor Data</h4>
            <a href="proses_ekspor.php?filter=<?= $filter ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                class="btn btn-primary" target="_blank">
                <i class="bx bx-download me-1"></i> Mulai Ekspor Data Ini
            </a>
        </div>

        <div class="card-header d-flex justify-content-start align-items-center flex-wrap gap-2">
            <a href="ekspor.php?filter=all" 
                class="btn rounded-pill <?= ($filter == 'all') ? 'btn-primary' : 'btn-outline-primary' ?>">
                Sepanjang masa
            </a>
        
            <a href="ekspor.php?filter=month" 
                class="btn rounded-pill <?= ($filter == 'month') ? 'btn-primary' : 'btn-outline-primary' ?>">
                Bulan ini
            </a>
            
            <a href="ekspor.php?filter=week" 
                class="btn rounded-pill <?= ($filter == 'week') ? 'btn-primary' : 'btn-outline-primary' ?>">
                Minggu ini
            </a>
            
            <a href="ekspor.php?filter=today" 
                class="btn rounded-pill <?= ($filter == 'today') ? 'btn-primary' : 'btn-outline-primary' ?>">
                Hari ini
            </a>
            
            <button type="button" class="btn rounded-pill <?= ($filter == 'custom') ? 'btn-primary' : 'btn-outline-primary' ?>" 
                data-bs-toggle="modal" data-bs-target="#modalFilter">
                Kustom
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="datatable" class="table table-hover">
                    <thead>
                        <tr class="text-center">
                            <th width="50px">#</th>
                            <th>Nomor Paspor</th>
                            <th>Nama Paspor</th>
                            <th>Tanggal Lahir</th>
                            <th>Jenis Paspor</th>
                            <th>Tanggal Pengambilan</th>
                            <th>Jenis Permohonan</th>
                            <th>Jenis Kelamin</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    <?php
                        $index = 1;
                        $query = "SELECT * FROM pengambilan $whereClause ORDER BY tanggal_pengambilan DESC";
                        $exec = mysqli_query($conn, $query);

                        if (mysqli_num_rows($exec) > 0) {
                            while ($post = mysqli_fetch_assoc($exec)) :
                    ?>
                        <tr>
                            <td><?= $index++; ?></td>
                            <td><?= $post['nomor_paspor'] ?? '-'; ?></td>
                            <td><?= $post['nama_paspor'] ?? '-'; ?></td>
                            <td><?= $post['tanggal_lahir'] ?? '-'; ?></td>
                            <td><?= $post['jenis_paspor'] ?? '-'; ?></td>
                            
                            <td><?= $post['tanggal_pengambilan'] ?? '-'; ?></td>
                            
                            <td><?= $post['jenis_permohonan'] ?? '-'; ?></td>
                            <td><?= $post['jenis_kelamin'] ?? '-'; ?></td>
                        </tr>
                    <?php 
                            endwhile; 
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Tidak ada data ditemukan untuk filter ini.</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFilter" tabindex="-1">
    <div class="modal-dialog">
        <form action="ekspor.php" method="GET" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tanggal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="filter" value="custom">
                
                <div class="mb-3">
                    <label>Dari</label>
                    <input type="date" name="start_date" class="form-control" required value="<?= $start_date ?>">
                </div>
                <div class="mb-3">
                    <label>Sampai</label>
                    <input type="date" name="end_date" class="form-control" required value="<?= $end_date ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<?php include ".includes/footer.php"; ?>