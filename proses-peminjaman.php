
<?php
require_once "auth.php";
wajibSiswa();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: peminjaman-saya.php");
    exit;
}

$id_user = (int) $_SESSION["id_user"];
$id_alat = (int) ($_POST["id_alat"] ?? 0);
$id_jadwal = (int) ($_POST["id_jadwal"] ?? 0);
$jumlah = (int) ($_POST["jumlah"] ?? 0);

function kembali($pesan) {
    $_SESSION["pesan"] = $pesan;
    header("Location: peminjaman-saya.php");
    exit;
}

if ($id_alat <= 0 || $id_jadwal <= 0 || $jumlah <= 0) {
    kembali("Data peminjaman tidak valid.");
}

try {
    $conn->begin_transaction();

    // Periksa alat dan stok
    $stmt = $conn->prepare("
        SELECT jumlah, status
        FROM alat
        WHERE id_alat = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $id_alat);
    $stmt->execute();

    $alat = $stmt->get_result()->fetch_assoc();

    if (!$alat || $alat["status"] !== "tersedia"
        || $jumlah > (int)$alat["jumlah"]) {
        throw new Exception("Alat tidak tersedia atau stok tidak mencukupi.");
    }

    // Periksa jadwal
    $stmt = $conn->prepare("
        SELECT tanggal, status
        FROM jadwal
        WHERE id_jadwal = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $id_jadwal);
    $stmt->execute();

    $jadwal = $stmt->get_result()->fetch_assoc();

    if (!$jadwal || $jadwal["status"] !== "tersedia"
        || $jadwal["tanggal"] < date("Y-m-d")) {
        throw new Exception("Jadwal tidak tersedia.");
    }

    // Cegah dua pengajuan menggunakan jadwal yang sama
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM peminjaman
        WHERE id_jadwal = ?
        AND status_peminjaman IN
        ('menunggu', 'disetujui', 'sedang_dipinjam')
    ");
    $stmt->bind_param("i", $id_jadwal);
    $stmt->execute();

    $cek = $stmt->get_result()->fetch_assoc();

    if ((int)$cek["total"] > 0) {
        throw new Exception("Jadwal sudah digunakan oleh pengajuan lain.");
    }

    // Simpan data peminjaman
    $stmt = $conn->prepare("
        INSERT INTO peminjaman
        (id_user, id_jadwal, tanggal_peminjaman, status_peminjaman)
        VALUES (?, ?, ?, 'menunggu')
    ");

    $stmt->bind_param(
        "iis",
        $id_user,
        $id_jadwal,
        $jadwal["tanggal"]
    );

    $stmt->execute();

    $id_peminjaman = $conn->insert_id;

    // Simpan detail alat yang dipinjam
    $stmt = $conn->prepare("
        INSERT INTO detail_peminjaman
        (id_peminjaman, id_alat, jumlah)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param(
        "iii",
        $id_peminjaman,
        $id_alat,
        $jumlah
    );

    $stmt->execute();

    // Buat notifikasi untuk siswa
    $pesan = "Pengajuan peminjaman alat berhasil dibuat.";

    $stmt = $conn->prepare("
        INSERT INTO notifikasi
        (id_user, pesan, status_baca)
        VALUES (?, ?, 'belum_dibaca')
    ");

    $stmt->bind_param("is", $id_user, $pesan);
    $stmt->execute();

    $conn->commit();

    kembali("Pengajuan berhasil! Silakan tunggu persetujuan pengelola.");

} catch (Throwable $e) {
    $conn->rollback();
    kembali("Pengajuan gagal: " . $e->getMessage());
}
?>
