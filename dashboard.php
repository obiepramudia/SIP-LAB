
<?php
require_once "auth.php";
wajibSiswa();

$idUser = $_SESSION["id_user"];
$nama = $_SESSION["nama"] ?? "Siswa";
$kelas = $_SESSION["kelas"] ?? "-";

// Menghitung jumlah alat tersedia
$queryAlat = $conn->query("
    SELECT COALESCE(SUM(jumlah), 0) AS total
    FROM alat
    WHERE status = 'tersedia'
");

$totalAlat = $queryAlat->fetch_assoc()["total"];

// Menghitung pengajuan yang menunggu
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM peminjaman
    WHERE id_user = ? AND status_peminjaman = 'menunggu'
");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$totalMenunggu = $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

// Menghitung seluruh peminjaman siswa
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM peminjaman
    WHERE id_user = ?
");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$totalPeminjaman = $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

// Mengambil lima peminjaman terbaru
$stmt = $conn->prepare("
    SELECT
        p.id_peminjaman,
        p.tanggal_peminjaman,
        p.status_peminjaman,
        j.waktu_mulai,
        j.waktu_selesai,
        GROUP_CONCAT(
            CONCAT(a.nama_alat, ' (', d.jumlah, ')')
            SEPARATOR ', '
        ) AS daftar_alat
    FROM peminjaman p
    LEFT JOIN jadwal j
        ON p.id_jadwal = j.id_jadwal
    LEFT JOIN detail_peminjaman d
        ON p.id_peminjaman = d.id_peminjaman
    LEFT JOIN alat a
        ON d.id_alat = a.id_alat
    WHERE p.id_user = ?
    GROUP BY
        p.id_peminjaman,
        p.tanggal_peminjaman,
        p.status_peminjaman,
        j.waktu_mulai,
        j.waktu_selesai
    ORDER BY p.id_peminjaman DESC
    LIMIT 5
");

$stmt->bind_param("i", $idUser);
$stmt->execute();
$riwayat = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - SIP-LAB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-mortarboard-fill me-2"></i>SIP-LAB</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSiswa" aria-controls="navbarSiswa" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSiswa">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="daftar-alat.php">Daftar Alat</a></li>
                <li class="nav-item"><a class="nav-link" href="peminjaman-saya.php">Peminjaman Saya</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small"><i class="bi bi-person-circle me-1"></i><?php echo e($nama); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <div class="p-4 p-md-5 mb-4 bg-primary text-white rounded-4 shadow-sm">
        <span class="badge bg-light text-primary mb-3">SELAMAT DATANG</span>
        <h1 class="fw-bold display-6">Sistem Informasi Peminjaman Alat Pembelajaran</h1>
        <p class="lead text-white-50 mb-4">Kelola peminjaman alat pembelajaran sekolah dengan mudah, cepat, dan terorganisir.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="peminjaman-saya.php" class="btn btn-light text-primary fw-semibold">Ajukan Peminjaman</a>
            <a href="daftar-alat.php" class="btn btn-outline-light">Lihat Daftar Alat</a>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-primary fs-3 mb-2"><i class="bi bi-box-seam"></i></div>
                <h2 class="fw-bold mb-1"><?php echo $totalAlat; ?></h2><p class="text-secondary mb-0">Alat Tersedia</p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-warning fs-3 mb-2"><i class="bi bi-clock-history"></i></div>
                <h2 class="fw-bold mb-1"><?php echo $totalMenunggu; ?></h2><p class="text-secondary mb-0">Menunggu Persetujuan</p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-success fs-3 mb-2"><i class="bi bi-clipboard-check"></i></div>
                <h2 class="fw-bold mb-1"><?php echo $totalPeminjaman; ?></h2><p class="text-secondary mb-0">Total Peminjaman</p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-info fs-3 mb-2"><i class="bi bi-people"></i></div>
                <h2 class="fw-bold mb-1"><?php echo e($kelas); ?></h2><p class="text-secondary mb-0">Kelas Siswa</p>
            </div></div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h2 class="h4 fw-bold mb-1">Peminjaman Terbaru</h2><p class="text-secondary mb-0">Daftar pengajuan peminjaman alat milikmu.</p></div>
        <a href="peminjaman-saya.php" class="btn btn-outline-primary btn-sm">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>No.</th><th>Nama Alat</th><th>Tanggal</th><th>Jadwal</th><th>Status</th></tr></thead>
                <tbody>
                <?php if ($riwayat->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $riwayat->fetch_assoc()): ?>
                        <?php
                        $status = $row["status_peminjaman"];
                        $warnaStatus = [
                            "menunggu" => "warning",
                            "disetujui" => "success",
                            "ditolak" => "danger",
                            "sedang_dipinjam" => "info",
                            "selesai" => "secondary"
                        ][$status] ?? "secondary";
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo e($row["daftar_alat"] ?? "-"); ?></td>
                            <td><?php echo date("d-m-Y", strtotime($row["tanggal_peminjaman"])); ?></td>
                            <td><?php echo date("H:i", strtotime($row["waktu_mulai"])) . " - " . date("H:i", strtotime($row["waktu_selesai"])); ?></td>
                            <td><span class="badge text-bg-<?php echo $warnaStatus; ?>"><?php echo ucfirst(str_replace("_", " ", $status)); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada riwayat peminjaman.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="text-center text-secondary small mt-5">SIP-LAB © 2026 - Sistem Informasi Peminjaman Alat Pembelajaran</footer>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $stmt->close(); ?>
