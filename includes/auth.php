<?php
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: ../auth/login.php");
    exit();
}
?>