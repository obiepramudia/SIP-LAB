<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_peminjaman_alat";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal!");
}

$conn->set_charset("utf8mb4");
?>