<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin() && !isPetugas()) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

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

$title = 'Manajemen Buku';
include '../../includes/header.php'
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manajemen Buku</h1>
    <div>
        <a href="tambah.php" class="btn btn-primary">Tambah Buku</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Daftar Buku</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Cari buku..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cari</button>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>ISBN</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['judul']}</td>
                            <td>{$row['penulis']}</td>
                            <td>{$row['penerbit']}</td>
                            <td>{$row['tahun_terbit']}</td>
                            <td>{$row['isbn']}</td>
                            <td>{$row['stok']}</td>
                            <td>
                                <a href='edit.php?id={$row['id_buku']}' class='btn btn-sm btn-warning'>Edit</a>
                                ";
                                
                        if (isAdmin()) {
                            echo "<a href='hapus.php?id={$row['id_buku']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>";
                        }
                                
                        echo "</td>
                        </tr>";
                        $no++;
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='8' class='text-center'>Tidak ada data buku</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'?>