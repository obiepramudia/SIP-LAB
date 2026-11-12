
<?php
require_once "auth.php";
wajibSiswa();

$idUser = $_SESSION["id_user"];

// Pesan dari proses pengajuan
$pesan = $_SESSION["pesan"] ?? "";
unset($_SESSION["pesan"]);

// Mengambil alat yang tersedia
$alat = $conn->query("
    SELECT id_alat, nama_alat, jumlah
    FROM alat
    WHERE status = 'tersedia' AND jumlah > 0
    ORDER BY nama_alat ASC
");

// Mengambil jadwal yang masih tersedia
$stmtJadwal = $conn->prepare("
    SELECT j.id_jadwal, j.tanggal, j.waktu_mulai, j.waktu_selesai
    FROM jadwal j
    WHERE j.tanggal >= CURDATE()
      AND j.status = 'tersedia'
      AND NOT EXISTS (
          SELECT 1
          FROM peminjaman p
          WHERE p.id_jadwal = j.id_jadwal
            AND p.status_peminjaman IN (
                'menunggu',
                'disetujui',
                'sedang_dipinjam'
            )
      )
    ORDER BY j.tanggal ASC, j.waktu_mulai ASC
");

$stmtJadwal->execute();
$jadwal = $stmtJadwal->get_result();

// Mengambil riwayat peminjaman
$stmtRiwayat = $conn->prepare("
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
");

$stmtRiwayat->bind_param("i", $idUser);
$stmtRiwayat->execute();
$riwayat = $stmtRiwayat->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - SIP-LAB</title>
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
            <a href="daftar-alat.php"><span>▣</span> Daftar Alat</a>
            <a href="peminjaman-saya.php" class="active"><span>▤</span> Peminjaman Saya</a>
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
            <div class="search-placeholder">SIP-LAB / Peminjaman Saya</div>
            <div class="topbar-user">
                <span>Portal Pengguna</span>
                <a href="logout.php">Keluar</a>
            </div>
        </header>

        <section class="content-area">

            <div class="page-intro">
                <span class="hero-label">LAYANAN PEMINJAMAN</span>
                <h1>Peminjaman Saya</h1>
                <p>
                    Ajukan peminjaman alat pembelajaran dan
                    pantau status pengajuanmu di sini.
                </p>
            </div>

            <?php if ($pesan !== ""): ?>
                <div class="alert">
                    <?php echo e($pesan); ?>
                </div>
            <?php endif; ?>

            <div class="section-heading">
                <div>
                    <h2>Formulir Peminjaman</h2>
                    <p>Lengkapi data berikut untuk mengajukan peminjaman.</p>
                </div>
            </div>

            <div class="form-card">

                <form action="proses-peminjaman.php" method="POST"
                      class="borrowing-form">

                    <div class="form-group">
                        <label for="id_alat">Pilih Alat</label>

                        <select name="id_alat" id="id_alat" required>
                            <option value="">-- Pilih Alat --</option>

                            <?php while ($item = $alat->fetch_assoc()): ?>
                                <option value="<?php echo $item["id_alat"]; ?>">
                                    <?php
                                    echo e($item["nama_alat"]) .
                                         " - Tersedia: " .
                                         (int)$item["jumlah"];
                                    ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah Alat</label>

                        <input
                            type="number"
                            name="jumlah"
                            id="jumlah"
                            min="1"
                            placeholder="Masukkan jumlah"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="id_jadwal">Pilih Jadwal</label>

                        <select name="id_jadwal" id="id_jadwal" required>
                            <option value="">-- Pilih Jadwal --</option>

                            <?php while ($item = $jadwal->fetch_assoc()): ?>
                                <option value="<?php echo $item["id_jadwal"]; ?>">
                                    <?php
                                    echo date("d-m-Y", strtotime($item["tanggal"])) .
                                         " | " .
                                         date("H:i", strtotime($item["waktu_mulai"])) .
                                         " - " .
                                         date("H:i", strtotime($item["waktu_selesai"]));
                                    ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="submit-borrowing">
                        Ajukan Peminjaman →
                    </button>

                </form>

                <p class="form-information">
                    Pengajuan akan berstatus menunggu sampai mendapatkan
                    persetujuan dari pengelola.
                </p>

            </div>

            <div class="section-heading history-heading">
                <div>
                    <h2>Riwayat Peminjaman</h2>
                    <p>Daftar pengajuan peminjaman yang pernah dilakukan.</p>
                </div>
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

                                <td><?php echo e($row["daftar_alat"] ?? "-"); ?></td>

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
                                    echo date("H:i", strtotime($row["waktu_mulai"])) .
                                         " - " .
                                         date("H:i", strtotime($row["waktu_selesai"]));
                                    ?>
                                </td>

                                <td>
                                    <span class="status-badge status-<?php echo e($row["status_peminjaman"]); ?>">
                                        <?php
                                        echo ucfirst(
                                            str_replace(
                                                "_",
                                                " ",
                                                $row["status_peminjaman"]
                                            )
                                        );
                                        ?>
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
$stmtJadwal->close();
$stmtRiwayat->close();
?>
