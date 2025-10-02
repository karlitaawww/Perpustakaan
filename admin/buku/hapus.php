<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';
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

// Check if book exists
$query = "SELECT * FROM buku WHERE id_buku = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

// Check if book is borrowed
$query_pinjam = "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = $id AND tanggal_dikembalikan IS NULL";
$result_pinjam = mysqli_query($conn, $query_pinjam);
$total_pinjam = mysqli_fetch_assoc($result_pinjam)['total'];

if ($total_pinjam > 0) {
    $_SESSION['error'] = 'Buku tidak dapat dihapus karena masih dipinjam';
    header("Location: index.php");
    exit();
}

// Delete book
$query_delete = "DELETE FROM buku WHERE id_buku = $id";
if (mysqli_query($conn, $query_delete)) {
    $_SESSION['success'] = 'Buku berhasil dihapus';
} else {
    $_SESSION['error'] = 'Gagal menghapus buku: ' . mysqli_error($conn);
}

header("Location: index.php");
exit();
?>