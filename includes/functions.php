<?php

require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {

    return isset($_SESSION['user_id']);
}

function isAdmin() {
    
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isPetugas() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'petugas';
}

function isAnggota() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'anggota';
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function redirectBasedOnRole() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header("Location: ../admin/dashboard.php");
        } elseif (isPetugas()) {
            header("Location: ../petugas/dashboard.php");
        } elseif (isAnggota()) {
            header("Location: ../anggota/dashboard.php");
        }
        exit();
    }
}

function hitungDenda($tanggal_kembali, $tanggal_dikembalikan) {
    $kembali = new DateTime($tanggal_kembali);
    $dikembalikan = new DateTime($tanggal_dikembalikan);
    
    if ($dikembalikan > $kembali) {
        $selisih = $kembali->diff($dikembalikan);
        $hari_terlambat = $selisih->days;
        return $hari_terlambat * 2000; // Denda Rp 2000 per hari
    }
    return 0;
}
?>