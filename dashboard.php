
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIP-LAB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">S</div>
            <div>
                <h3>SIP-LAB</h3>
                <span>Peminjaman Alat</span>
            </div>
        </div>

        <p class="sidebar-label">MENU UTAMA</p>

        <nav class="sidebar-menu">
            <a href="dashboard.php" class="active">
                <span>▦</span> Dashboard
            </a>

            <a href="daftar-alat.php">
                <span>▣</span> Daftar Alat
            </a>

            <a href="peminjaman-saya.php">
                <span>▤</span> Peminjaman Saya
            </a>
        </nav>

        <div class="sidebar-profile">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($nama, 0, 1)); ?>
            </div>

            <div class="profile-info">
                <strong><?php echo e($nama); ?></strong>
                <span>Siswa - <?php echo e($kelas); ?></span>
            </div>
        </div>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">
            <div class="search-placeholder">
                SIP-LAB / Dashboard
            </div>

            <div class="topbar-user">
                <span>Portal Siswa</span>
                <a href="logout.php">Keluar</a>
            </div>
        </header>

        <section class="content-area">

            <!-- HERO -->
            <div class="hero-banner">
                <div class="hero-text">
                    <span class="hero-label">SELAMAT DATANG, SISWA</span>

                    <h1>
                        Sistem Informasi<br>
                        Peminjaman Alat Pembelajaran
                    </h1>

                    <p>
                        Kelola peminjaman alat pembelajaran sekolah
                        dengan mudah, cepat, dan terorganisir.
                    </p>

                    <div class="hero-buttons">
                        <a href="peminjaman-saya.php" class="btn-white">
                            Ajukan Peminjaman
                        </a>

                        <a href="daftar-alat.php" class="btn-outline">
                            Lihat Daftar Alat
                        </a>
                    </div>
                </div>

                <div class="hero-decoration">▣</div>
            </div>

            <!-- STATISTIK -->
            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon blue">▣</div>
                    <h2><?php echo $totalAlat; ?></h2>
                    <p>Alat Tersedia</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">◷</div>
                    <h2><?php echo $totalMenunggu; ?></h2>
                    <p>Menunggu Persetujuan</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">▤</div>
                    <h2><?php echo $totalPeminjaman; ?></h2>
                    <p>Total Peminjaman</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">♙</div>
                    <h2><?php echo e($kelas); ?></h2>
                    <p>Kelas Siswa</p>
                </div>

            </div>

            <!-- RIWAYAT TERBARU -->
            <div class="section-heading">
                <div>
                    <h2>Peminjaman Terbaru</h2>
                    <p>Daftar pengajuan peminjaman alat milikmu.</p>
                </div>

                <a href="peminjaman-saya.php">Lihat Semua →</a>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Alat</th>
                            <th>Tanggal</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($riwayat->num_rows > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = $riwayat->fetch_assoc()): ?>

                            <tr>
                                <td><?php echo $no++; ?></td>

                                <td>
                                    <?php echo e($row["daftar_alat"] ?? "-"); ?>
                                </td>

                                <td>
                                    <?php
                                    echo date(
                                        "d-m-Y",
                                        strtotime($row["tanggal_peminjaman"])
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo date("H:i", strtotime($row["waktu_mulai"]))
                                        . " - " .
                                        date("H:i", strtotime($row["waktu_selesai"]));
                                    ?>
                                </td>

                                <td>
                                    <span class="status-badge status-<?php echo e($row["status_peminjaman"]); ?>">
                                        <?php echo ucfirst(str_replace("_", " ", $row["status_peminjaman"])); ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-data">
                                Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <footer class="page-footer">
                SIP-LAB © 2026 - Sistem Informasi Peminjaman Alat Pembelajaran
            </footer>

        </section>
    </main>

</div>

</body>
</html>

<?php
$stmt->close();
?>
