<?php
// require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// if (!isAdmin() && !isPetugas()) {
//     header("Location: ../../../auth/login.php");
//     exit();
// }

require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Get loan data
$query = "SELECT p.*, b.judul, a.username 
          FROM peminjaman p
          JOIN buku b ON p.id_buku = b.id_buku
          JOIN users a ON p.id_user = a.id_user
          WHERE p.id_pinjam = $id";
$result = mysqli_query($conn, $query);
$peminjaman = mysqli_fetch_assoc($result);

if (!$peminjaman) {
    header("Location: index.php");
    exit();
}

if ($peminjaman['tanggal_dikembalikan']) {
    $_SESSION['error'] = 'Buku sudah dikembalikan sebelumnya';
    header("Location: index.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_dikembalikan = $_POST['tanggal_dikembalikan'];
    
    if (empty($tanggal_dikembalikan)) {
        $errors[] = 'Tanggal dikembalikan harus diisi';
    } else {
        $denda = hitungDenda($peminjaman['tanggal_kembali'], $tanggal_dikembalikan);
        
        $query = "UPDATE peminjaman SET 
                  tanggal_dikembalikan = '$tanggal_dikembalikan', 
                  denda = $denda 
                  WHERE id_pinjam = $id";
        
        if (mysqli_query($conn, $query)) {
            // Update book stock
            $query_update = "UPDATE buku SET stok = stok + 1 WHERE id_buku = {$peminjaman['id_buku']}";
            mysqli_query($conn, $query_update);
            
            $_SESSION['success'] = 'Pengembalian buku berhasil dicatat';
            header("Location: index.php");
            exit();
        } else {
            $errors[] = 'Gagal mencatat pengembalian: ' . mysqli_error($conn);
        }
    }
}

$title = 'Pengembalian Buku';
include '../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Pengembalian Buku</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <h5>Detail Peminjaman</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Anggota</th>
                            <td><?php echo htmlspecialchars($peminjaman['username']); ?></td>
                        </tr>
                        <tr>
                            <th>Buku</th>
                            <td><?php echo htmlspecialchars($peminjaman['judul']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Pinjam</th>
                            <td><?php echo $peminjaman['tanggal_pinjam']; ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Kembali</th>
                            <td><?php echo $peminjaman['tanggal_kembali']; ?></td>
                        </tr>
                    </table>
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="tanggal_dikembalikan" class="form-label">Tanggal Dikembalikan</label>
                        <input type="date" class="form-control" id="tanggal_dikembalikan" name="tanggal_dikembalikan" value="<?php echo $_POST['tanggal_dikembalikan'] ?? date('Y-m-d'); ?>" required>
                    </div>
                    
                    <?php
                    $denda = hitungDenda($peminjaman['tanggal_kembali'], $_POST['tanggal_dikembalikan'] ?? date('Y-m-d'));
                    if ($denda > 0): ?>
                        <div class="alert alert-warning">
                            Denda yang harus dibayar: Rp <?php echo number_format($denda, 0, ',', '.'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-primary">Simpan Pengembalian</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>