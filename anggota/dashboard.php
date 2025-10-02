<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// if (isAnggota()) {
//     header("Location: ../../auth/login.php");
//     exit();
// }

require_once __DIR__ . '/../config/database.php';

// Hitung total buku
$query_buku = "SELECT COUNT(*) as total FROM buku";
$result_buku = mysqli_query($conn, $query_buku);
$total_buku = mysqli_fetch_assoc($result_buku)['total'];

// Hitung total anggota
$query_anggota = "SELECT COUNT(*) as total FROM users";
$result_anggota = mysqli_query($conn, $query_anggota);
$total_anggota = mysqli_fetch_assoc($result_anggota)['total'];

// Hitung total peminjaman aktif
$query_peminjaman = "SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_dikembalikan IS NULL";
$result_peminjaman = mysqli_query($conn, $query_peminjaman);
$total_peminjaman = mysqli_fetch_assoc($result_peminjaman)['total'];

// Hitung total denda belum dibayar
$query_denda = "SELECT SUM(denda) as total FROM peminjaman WHERE denda > 0 AND denda_dibayar = 0";
$result_denda = mysqli_query($conn, $query_denda);
$total_denda = mysqli_fetch_assoc($result_denda)['total'] ?? 0;

$title = 'Dashboard';
include '../includes/header.php'
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Buku</h5>
                <p class="card-text display-6"><?php echo $total_buku; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Anggota</h5>
                <p class="card-text display-6"><?php echo $total_anggota; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Peminjaman Aktif</h5>
                <p class="card-text display-6"><?php echo $total_peminjaman; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Total Denda</h5>
                <p class="card-text display-6">Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Peminjaman Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT p.*, a.username as nama_anggota, b.judul as judul_buku 
                                      FROM peminjaman p
                                      JOIN users a ON p.id_user = a.id_user
                                      JOIN buku b ON p.id_buku = b.id_buku
                                      ORDER BY p.tanggal_pinjam DESC LIMIT 5";
                            $result = mysqli_query($conn, $query);
                            
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['tanggal_dikembalikan'] ? 
                                    '<span class="badge bg-success">Dikembalikan</span>' : 
                                    '<span class="badge bg-warning text-dark">Dipinjam</span>';
                                    
                                echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['nama_anggota']}</td>
                                    <td>{$row['judul_buku']}</td>
                                    <td>{$row['tanggal_pinjam']}</td>
                                    <td>{$row['tanggal_kembali']}</td>
                                    <td>{$status}</td>
                                </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>