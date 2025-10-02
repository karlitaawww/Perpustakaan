<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAnggota()) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Search functionality
$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT * FROM buku";
if ($search) {
    $query .= " WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%' OR isbn LIKE '%$search%'";
}
$query .= " ORDER BY judul ASC";

$result = mysqli_query($conn, $query);

$title = 'Daftar Buku';
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Daftar Buku</h1>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Katalog Buku</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Cari buku..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cari</button>
                <?php if ($search): ?>
                    <a href="buku.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="row">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($row['judul']) . '</h5>
                            <p class="card-text">
                                <strong>Penulis:</strong> ' . htmlspecialchars($row['penulis']) . '<br>
                                <strong>Penerbit:</strong> ' . htmlspecialchars($row['penerbit']) . '<br>
                                <strong>Tahun:</strong> ' . $row['tahun_terbit'] . '<br>
                                <strong>ISBN:</strong> ' . $row['isbn'] . '<br>
                                <strong>Stok:</strong> ' . $row['stok'] . '
                            </p>
                        </div>
                    </div>
                </div>';
            }
            
            if (mysqli_num_rows($result) == 0) {
                echo '<div class="col-12 text-center">Tidak ada data buku</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>