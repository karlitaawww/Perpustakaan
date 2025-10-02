<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

if (!isPetugas()) {
    header("Location: ../../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Get book data
$query = "SELECT * FROM buku WHERE id_buku = $id";
$result = mysqli_query($conn, $query);
$buku = mysqli_fetch_assoc($result);

if (!$buku) {
    header("Location: index.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
    $stok = (int)$_POST['stok'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    
    // Validasi
    if (empty($judul)) $errors[] = 'Judul harus diisi';
    if (empty($penulis)) $errors[] = 'Penulis harus diisi';
    if ($stok < 0) $errors[] = 'Stok tidak valid';
    
    if (empty($errors)) {
        $query = "UPDATE buku SET 
                  judul = '$judul', 
                  penulis = '$penulis', 
                  penerbit = '$penerbit', 
                  tahun_terbit = '$tahun_terbit', 
                  isbn = '$isbn', 
                  stok = $stok,
                  kategori = '$kategori'
                  WHERE id_buku = $id";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Buku berhasil diperbarui';
            // Update book data
            $buku = array_merge($buku, [
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun_terbit,
                'isbn' => $isbn,
                'stok' => $stok,
                'kategori' => $kategori
            ]);
        } else {
            $errors[] = 'Gagal memperbarui buku: ' . mysqli_error($conn);
        }
    }
}

$title = 'Edit Buku';
include '../../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Buku</h4>
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
                        <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($buku['judul']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="penulis" class="form-label">Penulis</label>
                        <input type="text" class="form-control" id="penulis" name="penulis" value="<?php echo htmlspecialchars($buku['penulis']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                        <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($buku['isbn']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" value="<?php echo htmlspecialchars($buku['kategori']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?php echo htmlspecialchars($buku['stok']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>