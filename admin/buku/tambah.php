<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin() && !isPetugas()) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
    $stok = (int)$_POST['stok'];
    
    // Validasi
    if (empty($judul)) $errors[] = 'Judul harus diisi';
    if (empty($penulis)) $errors[] = 'Penulis harus diisi';
    if ($stok < 0) $errors[] = 'Stok tidak valid';
    
    if (empty($errors)) {
        $query = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, isbn, stok) 
                  VALUES ('$judul', '$penulis', '$penerbit', '$tahun_terbit', '$isbn', $stok)";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Buku berhasil ditambahkan';
            $_POST = []; // Clear form
        } else {
            $errors[] = 'Gagal menambahkan buku: ' . mysqli_error($conn);
        }
    }
}

$title = 'Tambah Buku';
include '../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Tambah Buku Baru</h4>
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
                        <label for="judul" class="form-label">Judul Buku</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="<?php echo $_POST['judul'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="penulis" class="form-label">Penulis</label>
                        <input type="text" class="form-control" id="penulis" name="penulis" value="<?php echo $_POST['penulis'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?php echo $_POST['penerbit'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                        <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo $_POST['tahun_terbit'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo $_POST['isbn'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?php echo $_POST['stok'] ?? 1; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>