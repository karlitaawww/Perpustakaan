<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

if (!isPetugas()) {
    header("Location: ../../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$errors = [];
$success = '';

// Get books and members for dropdown
$query_buku = "SELECT * FROM buku WHERE stok > 0 ORDER BY judul";
$result_buku = mysqli_query($conn, $query_buku);

$query_user = "SELECT * FROM users ORDER BY username";
$result_user = mysqli_query($conn, $query_user);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = (int)$_POST['id_buku'];
    $id_user = (int)$_POST['id_user'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $id_petugas = $_SESSION['user_id'];
    
    // Validasi
    if (empty($id_buku)) $errors[] = 'Buku harus dipilih';
    if (empty($id_user)) $errors[] = 'Username harus dipilih';
    if (empty($tanggal_pinjam)) $errors[] = 'Tanggal pinjam harus diisi';
    if (empty($tanggal_kembali)) $errors[] = 'Tanggal kembali harus diisi';
    
    // Check book availability
    if (empty($errors)) {
        $query_check = "SELECT stok FROM buku WHERE id_buku = $id_buku";
        $result_check = mysqli_query($conn, $query_check);
        $buku = mysqli_fetch_assoc($result_check);
        
        if ($buku['stok'] < 1) {
            $errors[] = 'Stok buku tidak tersedia';
        }
    }
    
    if (empty($errors)) {
        $query = "INSERT INTO peminjaman (id_buku, id_user, tanggal_pinjam, tanggal_kembali, id_petugas) 
                  VALUES ($id_buku, $id_user, '$tanggal_pinjam', '$tanggal_kembali', $id_petugas)";
        
        if (mysqli_query($conn, $query)) {
            // Update book stock
            $query_update = "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku";
            mysqli_query($conn, $query_update);
            
            $success = 'Peminjaman berhasil dicatat';
            $_POST = []; // Clear form
        } else {
            $errors[] = 'Gagal mencatat peminjaman: ' . mysqli_error($conn);
        }
    }
}

$title = 'Tambah Peminjaman';
include '../../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Tambah Peminjaman</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="id_buku" class="form-label">Buku</label>
                        <select class="form-select" id="id_buku" name="id_buku" required>
                            <option value="">Pilih Buku</option>
                            <?php while ($buku = mysqli_fetch_assoc($result_buku)): ?>
                                <option value="<?php echo $buku['id_buku']; ?>" <?php echo isset($_POST['id_buku']) && $_POST['id_buku'] == $buku['id_buku'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($buku['judul']); ?> (Stok: <?php echo $buku['stok']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_user" class="form-label">Anggota</label>
                        <select class="form-select" id="id_user" name="id_user" required>
                            <option value="">Pilih Anggota</option>
                            <?php while ($user = mysqli_fetch_assoc($result_user)): ?>
                                <option value="<?php echo $anggota['id_user']; ?>" <?php echo isset($_POST['id_user']) && $_POST['id_user'] == $user['id_user'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($anggota['username']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                        <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" value="<?php echo $_POST['tanggal_pinjam'] ?? date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                        <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="<?php echo $_POST['tanggal_kembali'] ?? date('Y-m-d', strtotime('+7 days')); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalPinjamInput = document.getElementById('tanggal_pinjam');
    const tanggalKembaliInput = document.getElementById('tanggal_kembali');
    
    if (tanggalPinjamInput && tanggalKembaliInput) {
        tanggalPinjamInput.addEventListener('change', function() {
            const pinjamDate = new Date(this.value);
            if (!isNaN(pinjamDate.getTime())) {
                const kembaliDate = new Date(pinjamDate);
                kembaliDate.setDate(kembaliDate.getDate() + 7); // 7 days loan period
                
                const year = kembaliDate.getFullYear();
                const month = String(kembaliDate.getMonth() + 1).padStart(2, '0');
                const day = String(kembaliDate.getDate()).padStart(2, '0');
                
                tanggalKembaliInput.value = `${year}-${month}-${day}`;
            }
        });
    }
});
</script>

<?php include '../../../includes/footer.php'; ?>