
<?php
require_once "auth.php";
wajibSiswa();

$query = $conn->query("
    SELECT id_alat, nama_alat, jumlah, kondisi, status
    FROM alat
    ORDER BY id_alat ASC
");

function namaKondisi($kondisi) {
    $daftar = [
        "baik" => "Baik",
        "rusak_ringan" => "Rusak Ringan",
        "rusak_berat" => "Rusak Berat"
    ];

    return $daftar[$kondisi] ?? $kondisi;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alat - SIP-LAB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

<div class="app-layout">

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
            <a href="dashboard.php"><span>▦</span> Dashboard</a>
            <a href="daftar-alat.php" class="active"><span>▣</span> Daftar Alat</a>
            <a href="peminjaman-saya.php"><span>▤</span> Peminjaman Saya</a>
        </nav>

        <div class="sidebar-profile">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($_SESSION["nama"], 0, 1)); ?>
            </div>
            <div class="profile-info">
                <strong><?php echo e($_SESSION["nama"]); ?></strong>
                <span>Pengguna SIP-LAB</span>
            </div>
        </div>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="search-placeholder">SIP-LAB / Daftar Alat</div>
            <div class="topbar-user">
                <span>Portal Pengguna</span>
                <a href="logout.php">Keluar</a>
            </div>
        </header>

        <section class="content-area">

            <div class="page-intro">
                <span class="hero-label">INFORMASI INVENTARIS</span>
                <h1>Daftar Alat Pembelajaran</h1>
                <p>
                    Lihat alat pembelajaran yang tersedia dan
                    kondisi alat sebelum mengajukan peminjaman.
                </p>
            </div>

            <div class="inventory-summary">
                <div>
                    <strong><?php echo $query->num_rows; ?></strong>
                    <span>Jenis Alat</span>
                </div>
                <div>
                    <strong>
                        <?php
                        $queryTotal = $conn->query("
                            SELECT COALESCE(SUM(jumlah), 0) AS total
                            FROM alat
                        ");
                        echo $queryTotal->fetch_assoc()["total"];
                        ?>
                    </strong>
                    <span>Total Unit Alat</span>
                </div>
            </div>

            <div class="section-heading inventory-heading">
                <div>
                    <h2>Inventaris Alat</h2>
                    <p>Daftar alat pembelajaran yang terdaftar dalam sistem.</p>
                </div>

                <a href="peminjaman-saya.php">Ajukan Peminjaman →</a>
            </div>

            <div class="equipment-grid">

                <?php if ($query->num_rows > 0): ?>

                    <?php while ($alat = $query->fetch_assoc()): ?>

                        <?php
                        $tersedia = (
                            $alat["status"] === "tersedia" &&
                            (int)$alat["jumlah"] > 0
                        );
                        ?>

                        <div class="equipment-card">

                            <div class="equipment-icon">
                                <?php
                                $ikon = [
                                    "Smart TV" => "▣",
                                    "Proyektor Portabel" => "▤",
                                    "Laptop" => "▰",
                                    "Kabel Rol" => "⌁"
                                ];

                                echo $ikon[$alat["nama_alat"]] ?? "▣";
                                ?>
                            </div>

                            <div class="equipment-status">
                                <span class="<?php echo $tersedia ? 'available' : 'unavailable'; ?>">
                                    <?php echo $tersedia ? "Tersedia" : "Tidak Tersedia"; ?>
                                </span>
                            </div>

                            <h3><?php echo e($alat["nama_alat"]); ?></h3>

                            <p class="equipment-description">
                                Alat pembelajaran sekolah
                            </p>

                            <div class="equipment-details">
                                <div>
                                    <span>Jumlah</span>
                                    <strong><?php echo (int)$alat["jumlah"]; ?> unit</strong>
                                </div>

                                <div>
                                    <span>Kondisi</span>
                                    <strong><?php echo e(namaKondisi($alat["kondisi"])); ?></strong>
                                </div>
                            </div>

                            <?php if ($tersedia): ?>
                                <a href="peminjaman-saya.php" class="equipment-button">
                                    Ajukan Peminjaman
                                </a>
                            <?php else: ?>
                                <button class="equipment-button disabled" disabled>
                                    Tidak Tersedia
                                </button>
                            <?php endif; ?>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <p>Belum ada data alat yang terdaftar.</p>

                <?php endif; ?>

            </div>

            <footer class="page-footer">
                SIP-LAB © 2026 - Sistem Informasi Peminjaman Alat Pembelajaran
            </footer>

        </section>
    </main>
</div>

</body>
</html>
