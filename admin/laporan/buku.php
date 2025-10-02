<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin()) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Filter
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$query = "SELECT * FROM buku WHERE 1=1";

if (!empty($filter_kategori)) {
    $query .= " AND kategori = '" . mysqli_real_escape_string($conn, $filter_kategori) . "'";
}

if (!empty($filter_tahun)) {
    $query .= " AND tahun_terbit = " . (int)$filter_tahun;
}

$query .= " ORDER BY judul ASC";
$result = mysqli_query($conn, $query);

// Get distinct categories and years for filters
$query_kategori = "SELECT DISTINCT kategori FROM buku WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori";
$result_kategori = mysqli_query($conn, $query_kategori);

$query_tahun = "SELECT DISTINCT tahun_terbit FROM buku ORDER BY tahun_terbit DESC";
$result_tahun = mysqli_query($conn, $query_tahun);

$title = 'Laporan Buku';
include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Laporan Data Buku</h1>
    <div>
        <!-- <a href="javascript:window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Laporan
        </a> -->
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php while ($row = mysqli_fetch_assoc($result_kategori)): ?>
                        <option value="<?php echo htmlspecialchars($row['kategori']); ?>" 
                            <?php echo $filter_kategori == $row['kategori'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['kategori']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="tahun" class="form-label">Tahun Terbit</label>
                <select class="form-select" id="tahun" name="tahun">
                    <option value="">Semua Tahun</option>
                    <?php while ($row = mysqli_fetch_assoc($result_tahun)): ?>
                        <option value="<?php echo $row['tahun_terbit']; ?>" 
                            <?php echo $filter_tahun == $row['tahun_terbit'] ? 'selected' : ''; ?>>
                            <?php echo $row['tahun_terbit']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <!-- <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="buku.php" class="btn btn-secondary">Reset</a> -->
            </div>
        </form>
    </div>
</div>

<div class="card mt-4 print-area">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Daftar Buku</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Kategori</th>
                        <th>ISBN</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $total_stok = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['judul']}</td>
                            <td>{$row['penulis']}</td>
                            <td>{$row['penerbit']}</td>
                            <td>{$row['tahun_terbit']}</td>
                            <td>{$row['kategori']}</td>
                            <td>{$row['isbn']}</td>
                            <td>{$row['stok']}</td>
                        </tr>";
                        $no++;
                        $total_stok += $row['stok'];
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='8' class='text-center'>Tidak ada data buku</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="7">Total</th>
                        <th><?php echo $total_stok; ?></th>
                    </tr>
                </tfoot>
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
    }
</style>

<?php include '../../includes/footer.php'; ?>