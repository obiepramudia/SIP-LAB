<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "koneksi.php";

function wajibSiswa() {
    if (!isset($_SESSION["id_user"]) ||
        $_SESSION["role"] !== "siswa") {
        header("Location: index.php");
        exit;
    }
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>