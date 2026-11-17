<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

// Buat CSRF Token sederhana jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF Token
    $postToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        $error = "Token keamanan tidak valid. Silakan muat ulang halaman.";
    } else {
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $username     = trim($_POST['username'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $password     = $_POST['password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        // Validasi input
        if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password) || empty($confirm_pass)) {
            $error = "Semua bidang formulir wajib diisi!";
        } elseif (strlen($username) < 4 || strlen($username) > 30 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = "Username minimal 4 karakter dan hanya boleh berisi huruf, angka, atau garis bawah (_).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format alamat email tidak valid!";
        } elseif (strlen($password) < 6) {
            $error = "Password minimal harus 6 karakter untuk keamanan!";
        } elseif ($password !== $confirm_pass) {
            $error = "Konfirmasi password tidak cocok dengan password yang dimasukkan!";
        } else {
            // Cek apakah username atau email sudah terdaftar (Prepared Statement - Anti SQL Injection)
            $checkStmt = mysqli_prepare($mysqli, "SELECT id, username, email FROM users WHERE username = ? OR email = ? LIMIT 1");
            mysqli_stmt_bind_param($checkStmt, "ss", $username, $email);
            mysqli_stmt_execute($checkStmt);
            $checkRes = mysqli_stmt_get_result($checkStmt);

            if ($checkRes && mysqli_num_rows($checkRes) > 0) {
                $existing = mysqli_fetch_assoc($checkRes);
                if (strcasecmp($existing['username'], $username) === 0) {
                    $error = "Username '" . htmlspecialchars($username) . "' sudah digunakan. Pilih username lain.";
                } else {
                    $error = "Alamat email '" . htmlspecialchars($email) . "' sudah terdaftar. Silakan gunakan email lain atau login.";
                }
                mysqli_stmt_close($checkStmt);
            } else {
                mysqli_stmt_close($checkStmt);

                // Hash password dengan algoritma BCRYPT standar industri
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $role = 'Teknisi Elektromedik';

                // Insert pengguna baru dengan Prepared Statement
                $insertStmt = mysqli_prepare($mysqli, "INSERT INTO users (username, password, nama_lengkap, email, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                mysqli_stmt_bind_param($insertStmt, "sssss", $username, $hashed_password, $nama_lengkap, $email, $role);

                if (mysqli_stmt_execute($insertStmt)) {
                    $new_id = mysqli_insert_id($mysqli);
                    mysqli_stmt_close($insertStmt);

                    // Catat ke log_akses
                    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
                    $logStmt = mysqli_prepare($mysqli, "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $act = "Registrasi Akun Baru Berhasil";
                    mysqli_stmt_bind_param($logStmt, "isssss", $new_id, $username, $nama_lengkap, $act, $ip, $ua);
                    mysqli_stmt_execute($logStmt);
                    mysqli_stmt_close($logStmt);

                    // Buat token baru dan alihkan ke login
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    header("Location: login.php?msg=registered&user=" . urlencode($username));
                    exit();
                } else {
                    $error = "Terjadi kesalahan sistem saat menyimpan akun. Silakan coba lagi.";
                    mysqli_stmt_close($insertStmt);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru (Sign Up) - SIMRS Inventory</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">
    
    <!-- Libraries -->
    <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">
    
    <!-- Sky Blue Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        body {
            background-color: #F0F4F9;
        }
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            background: linear-gradient(135deg, #F0F4F9 0%, #E0F2FE 100%);
        }
        .register-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(2, 132, 199, 0.08);
            max-width: 480px;
            width: 100%;
            padding: 2.25rem;
            position: relative;
        }
        .brand-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0284C7;
            text-decoration: none;
            margin-bottom: 0.25rem;
        }
        .brand-title i {
            color: #0284C7;
            font-size: 1.8rem;
        }
        .brand-subtitle {
            text-align: center;
            color: #64748B;
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
        }
        .btn-register-sky {
            background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
            color: #FFFFFF;
            border: none;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }
        .btn-register-sky:hover {
            background: linear-gradient(135deg, #0369A1 0%, #0284C7 100%);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35);
        }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            color: #0369A1;
            background: #E0F2FE;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="register-wrapper">
        <div class="register-card">
            <div class="text-center mb-3">
                <a href="login.php" class="brand-title">
                    <i class="bi bi-hospital"></i>
                    <span>SIMRS Alat</span>
                </a>
                <p class="brand-subtitle">Buat Akun Baru untuk Mengakses Data Inventaris</p>
                <div class="security-badge">
                    <i class="bi bi-shield-lock-fill"></i> Proteksi Password Terenkripsi BCrypt
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label small fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control border-start-0 ps-0" 
                               placeholder="Contoh: Syuja Ridhwan" required
                               value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : '' ?>">
                    </div>
                </div>

                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold text-dark">Username <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username" class="form-control border-start-0 ps-0" 
                               placeholder="Huruf, angka, min 4 karakter" required
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" 
                               placeholder="name@example.com" required
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label small fw-bold text-dark">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" 
                                   placeholder="Min 6 karakter" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" onclick="togglePass('password', 'eye1')">
                                <i class="bi bi-eye" id="eye1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="confirm_password" class="form-label small fw-bold text-dark">Ulangi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 border-end-0 ps-0" 
                                   placeholder="Ulangi password" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" onclick="togglePass('confirm_password', 'eye2')">
                                <i class="bi bi-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit Sign Up -->
                <button type="submit" class="btn-register-sky mt-3">
                    <i class="bi bi-person-check-fill me-1"></i>
                    <span>Daftar Akun (Sign Up)</span>
                </button>
            </form>

            <!-- Navigasi ke Halaman Login -->
            <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">Sudah memiliki akun terdaftar?</span>
                <a href="login.php" class="small fw-bold text-decoration-none text-primary ms-1">
                    Masuk Sekarang (Sign In) &rarr;
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
