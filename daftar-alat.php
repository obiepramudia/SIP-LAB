
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Alat - SIP-LAB</title>
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
                <li class="nav-item"><a class="nav-link active" href="daftar-alat.php">Daftar Alat</a></li>
                <li class="nav-item"><a class="nav-link" href="peminjaman-saya.php">Peminjaman Saya</a></li>
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
        <span class="badge text-bg-primary mb-2">INFORMASI INVENTARIS</span>
        <h1 class="fw-bold">Daftar Alat Pembelajaran</h1>
        <p class="text-secondary mb-0">Lihat alat pembelajaran yang tersedia dan kondisi alat sebelum mengajukan peminjaman.</p>
    </div>

    <?php
    $queryTotal = $conn->query("SELECT COALESCE(SUM(jumlah), 0) AS total FROM alat");
    $totalUnit = $queryTotal->fetch_assoc()["total"];
    ?>
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <div class="card border-0 shadow-sm"><div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary-subtle text-primary p-3 fs-3"><i class="bi bi-box-seam"></i></div>
                <div><h3 class="fw-bold mb-0"><?php echo $query->num_rows; ?></h3><span class="text-secondary">Jenis Alat</span></div>
            </div></div>
        </div>
        <div class="col-sm-6">
            <div class="card border-0 shadow-sm"><div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success-subtle text-success p-3 fs-3"><i class="bi bi-boxes"></i></div>
                <div><h3 class="fw-bold mb-0"><?php echo $totalUnit; ?></h3><span class="text-secondary">Total Unit Alat</span></div>
            </div></div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h2 class="h4 fw-bold mb-1">Inventaris Alat</h2><p class="text-secondary mb-0">Daftar alat pembelajaran yang terdaftar dalam sistem.</p></div>
        <a href="peminjaman-saya.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Ajukan Peminjaman</a>
    </div>

    <div class="row g-3">
        <?php if ($query->num_rows > 0): ?>
            <?php while ($alat = $query->fetch_assoc()): ?>
                <?php
                $tersedia = ($alat["status"] === "tersedia" && (int)$alat["jumlah"] > 0);
                $ikon = [
                    "Smart TV" => "bi-tv",
                    "Proyektor Portabel" => "bi-projector",
                    "Laptop" => "bi-laptop",
                    "Kabel Rol" => "bi-plug"
                ];
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="rounded-3 bg-primary-subtle text-primary p-3 fs-3">
                                    <i class="bi <?php echo $ikon[$alat["nama_alat"]] ?? "bi-box-seam"; ?>"></i>
                                </div>
                                <span class="badge text-bg-<?php echo $tersedia ? "success" : "danger"; ?>">
                                    <?php echo $tersedia ? "Tersedia" : "Tidak Tersedia"; ?>
                                </span>
                            </div>
                            <h3 class="h5 fw-bold"><?php echo e($alat["nama_alat"]); ?></h3>
                            <p class="text-secondary">Alat pembelajaran sekolah</p>
                            <div class="row g-2 border-top border-bottom py-3 mb-3">
                                <div class="col-6"><span class="small text-secondary d-block">Jumlah</span><strong><?php echo (int)$alat["jumlah"]; ?> unit</strong></div>
                                <div class="col-6"><span class="small text-secondary d-block">Kondisi</span><strong><?php echo e(namaKondisi($alat["kondisi"])); ?></strong></div>
                            </div>
                            <div class="mt-auto">
                                <?php if ($tersedia): ?>
                                    <a href="peminjaman-saya.php" class="btn btn-primary w-100">Ajukan Peminjaman</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>Tidak Tersedia</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-info mb-0">Belum ada data alat yang terdaftar.</div></div>
        <?php endif; ?>
    </div>
    <footer class="text-center text-secondary small mt-5">SIP-LAB © 2026 - Sistem Informasi Peminjaman Alat Pembelajaran</footer>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
