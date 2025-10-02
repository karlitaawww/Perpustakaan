<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAnggota()) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

$id_anggota = $_SESSION['user_id'];

// Filter status
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "SELECT p.*, b.judul as judul_buku 
          FROM peminjaman p
          JOIN buku b ON p.id_buku = b.id_buku
          WHERE p.id_anggota = $id_anggota";

if ($status == 'active') {
    $query .= " AND p.tanggal_dikembalikan IS NULL";
} elseif ($status == 'returned') {
    $query .= " AND p.tanggal_dikembalikan IS NOT NULL";
}

$query .= " ORDER BY p.tanggal_pinjam DESC";

$result = mysqli_query($conn, $query);

$title = 'Pinjaman Saya';
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Pinjaman Saya</h1>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Riwayat Peminjaman</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <div class="btn-group" role="group">
                <a href="?status=all" class="btn btn-outline-primary <?php echo $status == 'all' ? 'active' : ''; ?>">Semua</a>
                <a href="?status=active" class="btn btn-outline-primary <?php echo $status == 'active' ? 'active' : ''; ?>">Aktif</a>
                <a href="?status=returned" class="btn btn-outline-primary <?php echo $status == 'returned' ? 'active' : ''; ?>">Selesai</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
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
                            <td>{$row['judul_buku']}</td>
                            <td>{$row['tanggal_pinjam']}</td>
                            <td>{$row['tanggal_kembali']}</td>
                            <td>{$row['tanggal_dikembalikan']}</td>
                            <td>{$denda}</td>
                            <td>{$status}</td>
                        </tr>";
                        $no++;
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='7' class='text-center'>Tidak ada data peminjaman</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>