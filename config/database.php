<?php
$host = 'localhost';
$username = 'root'; 
$password = ''; // Sesuaikan dengan password MySQL Anda
$database = 'perpustakaan';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>