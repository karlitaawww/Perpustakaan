<?php
// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

// Redirect jika sudah login
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if ($_SESSION['role'] === 'admin') {
            header("Location: /admin/dashboard.php");
        } else {
            header("Location: /anggota/profil.php");
        }
        exit();
    }
}

// Redirect jika belum login
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: /auth/login.php");
        exit();
    }
}
?>