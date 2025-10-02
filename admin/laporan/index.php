<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin()) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Set default date range (this month)
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');
$filter_type = 'this_month';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filter_type = $_POST['filter_type'] ?? 'this_month';
    
    switch ($filter_type) {
        case 'custom':
            $start_date = $_POST['start_date'] ?? $start_date;
            $end_date = $_POST['end_date'] ?? $end_date;
            break;
        case 'this_month':
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            break;
        case 'last_month':
            $start_date = date('Y-m-01', strtotime('-1 month'));
            $end_date = date('Y-m-t', strtotime('-1 month'));
            break;
        case 'this_year':
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
            break;
    }
}

// Query to get loan reports
$query = "SELECT p.*, a.username as nama_anggota, b.judul as judul_buku 
          FROM peminjaman p
          JOIN users a ON p.id_user = a.id_user
          JOIN buku b ON p.id_buku = b.id_buku
          WHERE p.tanggal_pinjam BETWEEN '$start_date' AND '$end_date'
          ORDER BY p.tanggal_pinjam DESC";
$result = mysqli_query($conn, $query);

// Query to get summary statistics
$summary_query = "SELECT 
                    COUNT(*) as total_peminjaman,
                    SUM(CASE WHEN tanggal_dikembalikan is null THEN 1 ELSE 0 END) as sedang_dipinjam,
                    SUM(CASE WHEN tanggal_dikembalikan is not null THEN 1 ELSE 0 END) as sudah_kembali
                  FROM peminjaman
                  WHERE tanggal_pinjam BETWEEN '$start_date' AND '$end_date'";
$summary_result = mysqli_query($conn, $summary_query);
$summary = mysqli_fetch_assoc($summary_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <h3>Laporan Peminjaman</h3>
        
        <div class="card">
            <div class="card-header">
                <h4>Filter Laporan</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Jenis Laporan:</label>
                        <select name="filter_type" class="form-control" onchange="this.form.submit()">
                            <option value="this_month" <?= $filter_type == 'this_month' ? 'selected' : '' ?>>Bulan Ini</option>
                            <option value="last_month" <?= $filter_type == 'last_month' ? 'selected' : '' ?>>Bulan Lalu</option>
                            <option value="this_year" <?= $filter_type == 'this_year' ? 'selected' : '' ?>>Tahun Ini</option>
                            <option value="custom" <?= $filter_type == 'custom' ? 'selected' : '' ?>>Custom Range</option>
                        </select>
                    </div>
                    
                    <?php if ($filter_type == 'custom'): ?>
                    <div class="form-group">
                        <label>Dari Tanggal:</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sampai Tanggal:</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control">
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h4>Ringkasan</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Total Peminjaman</h5>
                            <?= $summary['total_peminjaman'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Sedang Dipinjam</h5>
                            <?= $summary['sedang_dipinjam'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Sudah Kembali</h5>
                            <?= $summary['sudah_kembali'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h4>Detail Peminjaman</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pinjam</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Status</th>
                            <th>Tanggal Kembali</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                    <td><?= $row['nama_anggota'] ?></td>
                                    <td><?= $row['judul_buku'] ?></td>
                                    <td>                                      
                                            <?= (!isset($row['tanggal_dikembalikan']) ? 'dipinjam' : 'dikembalikan') ?> 
                                    </td>
                                    <td><?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data peminjaman pada periode ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>