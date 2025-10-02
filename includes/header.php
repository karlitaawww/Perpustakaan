<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan - <?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .min-height {
    min-height: 75vh;
}
    </style>
</head>
<body>
    

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Perpustakaan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isAdmin() || isPetugas()): ?>
                           <a class="nav-link" href="/perpustakaan/admin/dashboard.php">Dashboard</a>
                            <a class="nav-link" href="/perpustakaan/admin/buku/index.php">Buku</a>
                            <a class="nav-link" href="/perpustakaan/admin/anggota/index.php">Anggota</a>
                            <a class="nav-link" href="/perpustakaan/admin/peminjaman/index.php">Peminjaman</a>
                            <a class="nav-link" href="/perpustakaan/admin/laporan/index.php">Laporan</a>

                    <?php elseif (isAnggota()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../anggota/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../anggota/profil.php">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../anggota/buku.php">Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../anggota/pinjaman.php">Pinjaman </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="nav-link">Halo, <?php echo $_SESSION['nama']; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/perpustakaan/admin/tambah_user.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/perpustakaan/auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/perpustakaan/auth/register.php">Register</a>
                        </li>    
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4 min-height">
   