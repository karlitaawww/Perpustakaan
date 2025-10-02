<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin()) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "SELECT p.*, a.username as nama_username, b.judul as judul_buku 
          FROM peminjaman p
          JOIN users a ON p.id_user = a.id_user
          JOIN buku b ON p.id_buku = b.id_buku
          WHERE p.tanggal_pinjam BETWEEN '$start_date' AND '$end_date'";

if ($status == 'active') {
    $query .= " AND p.tanggal_dikembalikan IS NULL";
} elseif ($status == 'returned') {
    $query .= " AND p.tanggal_dikembalikan IS NOT NULL";
}

$query .= " ORDER BY p.tanggal_pinjam DESC";
$result = mysqli_query($conn, $query);

// Hitung statistik
$query_stat = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN tanggal_dikembalikan IS NULL THEN 1 ELSE 0 END) as aktif,
                SUM(denda) as total_denda
               FROM peminjaman
               WHERE tanggal_pinjam BETWEEN '$start_date' AND '$end_date'";
$result_stat = mysqli_query($conn, $query_stat);
$statistik = mysqli_fetch_assoc($result_stat);

$title = 'Laporan Peminjaman';
include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Laporan Peminjaman</h1>
    <div>
        <a href="javascript:window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Laporan
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Dari Tanggal</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Sampai Tanggal</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                    <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="returned" <?php echo $status == 'returned' ? 'selected' : ''; ?>>Dikembalikan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="peminjaman.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Peminjaman</h5>
                        <p class="card-text display-4"><?php echo $statistik['total']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Masih Dipinjam</h5>
                        <p class="card-text display-4"><?php echo $statistik['aktif']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Denda</h5>
                        <p class="card-text display-4">Rp <?php echo number_format($statistik['total_denda'], 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 print-area">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Detail Peminjaman</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Dikembalikan</th>
                        <th>Denda</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status = $row['tanggal_dikembalikan'] ? 
                            '<span class="badge bg-success">Dikembalikan</span>' : 
                            '<span class="badge bg-warning text-dark">Dipinjam</span>';
                            
                        $denda = $row['denda'] > 0 ? 'Rp ' . number_format($row['denda'], 0, ',', '.') : '-';
                        
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama_anggota']}</td>
                            <td>{$row['judul_buku']}</td>
                            <td>{$row['tanggal_pinjam']}</td>
                            <td>{$row['tanggal_kembali']}</td>
                            <td>{$row['tanggal_dikembalikan'] ?? '-'}</td>
                            <td>{$denda}</td>
                            <td>{$status}</td>
                        </tr>";
                        $no++;
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='8' class='text-center'>Tidak ada data peminjaman</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        Dicetak pada <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .table {
            font-size: 12px;
        }
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
        }
        .table-dark {
            background-color: #f8f9fa !important;
            color: #000 !important;
        }
        .card-footer {
            display: block !important;
        }
        .bg-success, .bg-warning, .bg-danger {
            color: #000 !important;
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>

<?php include '../../includes/footer.php'; ?>