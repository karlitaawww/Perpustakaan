<?php
session_start();
// require_once __DIR__ . '../../includes/auth.php';
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

$query = "SELECT * FROM users WHERE role = 'anggota'";
if ($search) {
    $query .= " and username LIKE '%$search%' OR email LIKE '%$search%'";
}
$query .= " ORDER BY username ASC";

$result = mysqli_query($conn, $query);

$title = 'Manajemen Anggota';
include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manajemen Anggota</h1>
    <div>
        <a href="tambah.php" class="btn btn-primary">Tambah Anggota</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Daftar Anggota</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Cari anggota..." value="<?php echo htmlspecialchars($search); ?>">
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
                        <th>Username</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>
                                <a href='edit.php?id={$row['id_user']}' class='btn btn-sm btn-warning'>Edit</a>
                                ";
                                
                        if (isAdmin()) {
                            echo "<a href='hapus.php?id={$row['id_user']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>";
                        }
                                
                        echo "</td>
                        </tr>";
                        $no++;
                    }
                    
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='7' class='text-center'>Tidak ada data anggota</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>