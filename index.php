
<?php
session_start();
require_once "koneksi.php";

// Jika siswa sudah login, langsung ke dashboard
if (isset($_SESSION["id_user"]) && $_SESSION["role"] === "siswa") {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {

        $stmt = $conn->prepare("
            SELECT
                u.id_user,
                u.username,
                u.password,
                u.nama,
                u.role,
                s.nama_siswa,
                s.kelas
            FROM users u
            LEFT JOIN siswa s ON u.id_user = s.id_user
            WHERE u.username = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {

            // Mendukung password biasa maupun password hash
            $passwordBenar =
                password_verify($password, $user["password"]) ||
                hash_equals((string)$user["password"], $password);

            if ($passwordBenar && $user["role"] === "siswa") {

                session_regenerate_id(true);

                $_SESSION["id_user"] = $user["id_user"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["nama"] = $user["nama_siswa"] ?: $user["nama"];
                $_SESSION["kelas"] = $user["kelas"] ?: "-";

                header("Location: dashboard.php");
                exit;

            } elseif ($user["role"] !== "siswa") {
                $error = "Halaman ini hanya dapat diakses oleh akun siswa.";
            } else {
                $error = "Username atau password yang kamu masukkan salah.";
            }

        } else {
            $error = "Username atau password yang kamu masukkan salah.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pengguna - SIP-LAB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-xl-10">
              <div class="row g-0 bg-white rounded-4 shadow-lg overflow-hidden">
            <section class="col-lg-6 bg-primary text-white p-4 p-md-5 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <div class="bg-white text-primary rounded-3 fw-bold fs-3 px-3 py-2">S</div>
                        <div>
                            <h3 class="fw-bold mb-0">SIP-LAB</h3>
                            <span class="text-white-50">Sistem Peminjaman Alat</span>
                        </div>
                    </div>
                    <span class="badge bg-light text-primary mb-3">PORTAL PENGGUNA</span>
                    <h1 class="display-6 fw-bold mb-3">Belajar Lebih Mudah dengan Teknologi.</h1>
                    <p class="text-white-50 fs-5">
                        Akses informasi alat pembelajaran dan ajukan peminjaman
                        alat sekolah dengan mudah melalui sistem informasi SIP-LAB.
                    </p>
                    <div class="mt-5 text-center">
                        <i class="bi bi-laptop display-1"></i>
                        <i class="bi bi-projector display-1 ms-3"></i>
                    </div>
                </div>
                <div class="small text-white-50 mt-5">SIP-LAB © 2026</div>
            </section>

            <section class="col-lg-6 p-4 p-md-5 d-flex align-items-center">
                <div class="w-100">
                    <div class="d-lg-none text-primary fw-bold fs-4 mb-4">SIP-LAB</div>
                    <span class="text-primary fw-semibold small">SELAMAT DATANG</span>
                    <h2 class="fw-bold mt-2 mb-3">PORTAL PENGGUNA</h2>
                    <p class="text-secondary mb-4">
                        Silakan masuk menggunakan akun yang telah terdaftar
                        pada Sistem Informasi Peminjaman Alat.
                    </p>

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control form-control-lg" id="username"
                                   name="username" placeholder="Masukkan username"
                                   value="<?php echo htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                                   autocomplete="username" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group input-group-lg">
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Masukkan password" autocomplete="current-password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Masuk ke Dashboard <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="alert alert-info mt-4 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Gunakan username dan password akun yang telah terdaftar pada sistem.
                    </div>
                </div>
            </section>
              </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function () {
            const password = document.getElementById("password");
            const icon = this.querySelector("i");
            const tampil = password.type === "password";
            password.type = tampil ? "text" : "password";
            icon.className = tampil ? "bi bi-eye-slash" : "bi bi-eye";
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
