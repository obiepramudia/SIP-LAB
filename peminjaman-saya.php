
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peminjaman Saya - SIP-LAB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-mortarboard-fill me-2"></i>SIP-LAB</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSiswa" aria-controls="navbarSiswa" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSiswa">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="daftar-alat.php">Daftar Alat</a></li>
                <li class="nav-item"><a class="nav-link active" href="peminjaman-saya.php">Peminjaman Saya</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small"><i class="bi bi-person-circle me-1"></i><?php echo e($_SESSION["nama"]); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">LAYANAN PEMINJAMAN</span>
        <h1 class="fw-bold">Peminjaman Saya</h1>
        <p class="text-secondary mb-0">Ajukan peminjaman alat pembelajaran dan pantau status pengajuanmu di sini.</p>
    </div>

    <?php if ($pesan !== ""): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo e($pesan); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <h2 class="h4 fw-bold mb-1">Formulir Peminjaman</h2>
        <p class="text-secondary">Lengkapi data berikut untuk mengajukan peminjaman.</p>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4 p-lg-5">
            <form action="proses-peminjaman.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="id_alat" class="form-label fw-semibold">Pilih Alat</label>
                        <select name="id_alat" id="id_alat" class="form-select" required>
                            <option value="">-- Pilih Alat --</option>
                            <?php while ($item = $alat->fetch_assoc()): ?>
                                <option value="<?php echo $item["id_alat"]; ?>">
                                    <?php echo e($item["nama_alat"]) . " - Tersedia: " . (int)$item["jumlah"]; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah" class="form-label fw-semibold">Jumlah Alat</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control"
                               min="1" placeholder="Masukkan jumlah" required>
                    </div>
                    <div class="col-12">
                        <label for="id_jadwal" class="form-label fw-semibold">Pilih Jadwal</label>
                        <select name="id_jadwal" id="id_jadwal" class="form-select" required>
                            <option value="">-- Pilih Jadwal --</option>
                            <?php while ($item = $jadwal->fetch_assoc()): ?>
                                <option value="<?php echo $item["id_jadwal"]; ?>">
                                    <?php
                                    echo date("d-m-Y", strtotime($item["tanggal"])) . " | " .
                                         date("H:i", strtotime($item["waktu_mulai"])) . " - " .
                                         date("H:i", strtotime($item["waktu_selesai"]));
                                    ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            Ajukan Peminjaman <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="alert alert-light border mt-4 mb-0">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Pengajuan akan berstatus menunggu sampai mendapatkan persetujuan dari pengelola.
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h2 class="h4 fw-bold mb-1">Riwayat Peminjaman</h2>
        <p class="text-secondary">Daftar pengajuan peminjaman yang pernah dilakukan.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>No.</th><th>Nama Alat</th><th>Tanggal</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr>
                </thead>
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
                            <td>
                                <?php if ($status === "menunggu"): ?>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="edit-peminjaman.php?id=<?php echo (int)$row["id_peminjaman"]; ?>"
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </a>
                                        <form action="proses-batal-peminjaman.php" method="POST"
                                              onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?');">
                                            <input type="hidden" name="id_peminjaman" value="<?php echo (int)$row["id_peminjaman"]; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle me-1"></i>Batalkan
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-secondary small">Tidak tersedia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada riwayat peminjaman.</td></tr>
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
<?php
$stmtJadwal->close();
$stmtRiwayat->close();
?>
