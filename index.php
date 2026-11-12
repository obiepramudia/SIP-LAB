
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Pengguna - SIP-LAB</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

    <div class="login-container">

        <!-- BAGIAN KIRI -->
        <div class="login-illustration">

            <div class="login-brand">
                <div class="login-brand-icon">S</div>
                <div>
                    <h3>SIP-LAB</h3>
                    <span>Sistem Peminjaman Alat</span>
                </div>
            </div>

            <div class="illustration-content">
                <span class="illustration-label">
                    PORTAL PENGGUNA
                </span>

                <h1>
                    Belajar Lebih Mudah
                    dengan Teknologi.
                </h1>

                <p>
                    Akses informasi alat pembelajaran dan ajukan
                    peminjaman alat sekolah dengan mudah melalui
                    sistem informasi SIP-LAB.
                </p>

                <div class="illustration-symbol">
                    <div class="symbol-box">▣</div>
                    <div class="symbol-circle"></div>
                </div>
            </div>

            <div class="illustration-footer">
                SIP-LAB © 2026
            </div>

        </div>

        <!-- BAGIAN KANAN -->
        <div class="login-form-section">

            <div class="login-form-box">

                <div class="login-mobile-brand">
                    <div class="login-brand-icon">S</div>
                    <strong>SIP-LAB</strong>
                </div>

                <span class="login-welcome">
                    SELAMAT DATANG
                </span>

                <h2>PORTAL PENGGUNA</h2>

                <p class="login-description">
                    Silakan masuk menggunakan akun
                    yang telah terdaftar pada Sistem Informasi Peminjaman Alat.
                </p>

                <?php if ($error !== ""): ?>
                    <div class="login-error">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php">

                    <label for="username">Username</label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        value="<?php echo htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                        autocomplete="username"
                        required
                    >

                    <label for="password">Password</label>

                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()"
                            aria-label="Tampilkan password"
                        >
                            Lihat
                        </button>
                    </div>

                    <button type="submit" class="login-button">
                        Masuk ke Dashboard
                        <span>→</span>
                    </button>

                </form>

                <div class="login-note">
                    <span>ⓘ</span>
                     Gunakan username dan password akun yang
    telah terdaftar pada sistem.
                </div>

            </div>

            <div class="login-bottom">
                Sistem Informasi Peminjaman Alat Pembelajaran
            </div>

        </div>

    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const button = document.querySelector(".toggle-password");

            if (password.type === "password") {
                password.type = "text";
                button.textContent = "Sembunyikan";
            } else {
                password.type = "password";
                button.textContent = "Lihat";
            }
        }
    </script>

</body>
</html>
