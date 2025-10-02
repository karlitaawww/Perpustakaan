<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin()) {
    header("Location: /../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';


$query = "SELECT * FROM users WHERE 1=1";


$query .= " ORDER BY username ASC";
$result = mysqli_query($conn, $query);

$title = 'Laporan Anggota';
include '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Laporan Data Anggota</h1>
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
    <!-- <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="kelas" class="form-label">Kelas/Jurusan</label>
                <select class="form-select" id="kelas" name="kelas">
                    <option value="">Semua Kelas</option>
                    <?php while ($row = mysqli_fetch_assoc($result_kelas)): ?>
                        <option value="<?php echo htmlspecialchars($row['kelas']); ?>" 
                            <?php echo $filter_kelas == $row['kelas'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['kelas']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="anggota.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div> -->
</div>

<div class="card mt-4 print-area">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Daftar Anggota</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Kelas</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $total_anggota = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['nim']}</td>
                            <td>{$row['kelas']}</td>
                            <td>{$row['alamat']}</td>
                            <td>{$row['no_hp']}</td>
                        </tr>";
                        $no++;
                        $total_user++;
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='6' class='text-center'>Tidak ada data anggota</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="5">Total Anggota</th>
                        <th><?php echo $total_user; ?></th>
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

<?php include '/../../includes/footer.php'; ?>