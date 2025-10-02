<?php
session_start();
// require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdmin()) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Check if member exists
$query = "SELECT * FROM users WHERE id_user = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

// Check if member has active loans
$query_pinjam = "SELECT COUNT(*) as total FROM peminjaman WHERE id_user = $id AND tanggal_dikembalikan IS NULL";
$result_pinjam = mysqli_query($conn, $query_pinjam);
$total_pinjam = mysqli_fetch_assoc($result_pinjam)['total'];

if ($total_pinjam > 0) {
    $_SESSION['error'] = 'Anggota tidak dapat dihapus karena masih memiliki pinjaman aktif';
    header("Location: index.php");
    exit();
}

// Delete member
$query_delete = "DELETE FROM users WHERE id_user = $id";
if (mysqli_query($conn, $query_delete)) {
    $_SESSION['success'] = 'Anggota berhasil dihapus';
} else {
    $_SESSION['error'] = 'Gagal menghapus anggota: ' . mysqli_error($conn);
}

header("Location: index.php");
exit();
?>