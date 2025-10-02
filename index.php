<?php
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirectBasedOnRole();
}

$title = 'Sistem Informasi Perpustakaan';
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/style_index.css">
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mb-4">Sistem Informasi Perpustakaan</h1>
            <p class="lead mb-5">Selamat datang di sistem informasi perpustakaan digital kami. Silakan login untuk mengakses fitur lengkap.</p>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Admin</h2>
                            <p class="card-text">Login sebagai administrator untuk mengelola seluruh sistem.</p>
                            <a href="auth/login.php" class="btn btn-primary">Login Admin</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Petugas</h2>
                            <p class="card-text">Login sebagai petugas untuk mengelola peminjaman buku.</p>
                            <a href="auth/login.php" class="btn btn-primary">Login Petugas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Anggota</h2>
                            <p class="card-text">Login sebagai anggota untuk melihat buku dan riwayat pinjam.</p>
                            <a href="auth/login.php" class="btn btn-primary">Login Anggota</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Tentang Kami</h2>
                            <p class="card-text">Pelajari lebih lanjut tentang perpustakaan kami.</p>
                            <a href="auth/info.php" class="btn btn-primary">Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   
</body>
</html>

<?php include 'includes/footer.php'; ?>