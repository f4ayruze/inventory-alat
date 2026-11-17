<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");

// Jika sudah memiliki sesi login aktif, arahkan langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Silakan masukkan username dan password Anda!";
    } else {
        // Query menggunakan Prepared Statement untuk mencegah SQL Injection
        $stmt = mysqli_prepare($mysqli, "SELECT id, username, password, nama_lengkap, email, role FROM users WHERE username = ? OR email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Verifikasi Password (BCrypt standar industri atau MD5 fallback untuk akun lawas)
            $password_valid = password_verify($password, $user['password']) || ($user['password'] === md5($password)) || ($user['password'] === $password);

            if ($password_valid) {
                // Regenerasi session ID untuk mencegah serangan Session Fixation
                session_regenerate_id(true);

                $_SESSION['user_id']      = $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email']        = $user['email'];
                $_SESSION['role']         = $user['role'];

                // Update waktu login terakhir
                $upStmt = mysqli_prepare($mysqli, "UPDATE users SET terakhir_login = NOW() WHERE id = ?");
                mysqli_stmt_bind_param($upStmt, "i", $user['id']);
                mysqli_stmt_execute($upStmt);
                mysqli_stmt_close($upStmt);

                // Catat ke log_akses
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
                $logStmt = mysqli_prepare($mysqli, "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $act = "Login Berhasil ke Sistem";
                mysqli_stmt_bind_param($logStmt, "isssss", $user['id'], $user['username'], $user['nama_lengkap'], $act, $ip, $ua);
                mysqli_stmt_execute($logStmt);
                mysqli_stmt_close($logStmt);

                header("Location: index.php");
                exit();
            } else {
                // Catat kegagalan login untuk audit keamanan
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
                $failStmt = mysqli_prepare($mysqli, "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) VALUES (NULL, ?, 'Percobaan Gagal', 'Gagal Login: Password Salah', ?, ?, NOW())");
                mysqli_stmt_bind_param($failStmt, "sss", $username, $ip, $ua);
                mysqli_stmt_execute($failStmt);
                mysqli_stmt_close($failStmt);

                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            if ($stmt) mysqli_stmt_close($stmt);
            $error = "Akun tidak ditemukan! Pastikan username atau email sudah terdaftar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - SIMRS Inventory Alat Medis</title>
    
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
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            background: linear-gradient(135deg, #F0F4F9 0%, #E0F2FE 100%);
        }
        .login-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(2, 132, 199, 0.08);
            max-width: 440px;
            width: 100%;
            padding: 2.5rem;
            position: relative;
            z-index: 2;
        }
        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0284C7;
            text-decoration: none;
            margin-bottom: 0.35rem;
        }
        .login-brand i {
            color: #0284C7;
            font-size: 1.8rem;
        }
        .login-subtitle {
            text-align: center;
            color: #64748B;
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
        }
        .btn-sky {
            background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
            color: #FFFFFF;
            border: none;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }
        .btn-sky:hover {
            background: linear-gradient(135deg, #0369A1 0%, #0284C7 100%);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.35);
        }
        .btn-signup-outline {
            border: 2px solid #BAE6FD;
            background: #F0F9FF;
            color: #0284C7;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-signup-outline:hover {
            background: #E0F2FE;
            color: #0369A1;
            border-color: #0284C7;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <a href="login.php" class="login-brand">
                <i class="bi bi-hospital"></i>
                <span>SIMRS Alat</span>
            </a>
            <p class="login-subtitle">Masuk dengan Akun yang Telah Terdaftar</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'registered'): ?>
                    <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                        <span>Registrasi akun berhasil! Silakan masuk dengan akun yang baru Anda buat.</span>
                    </div>
                <?php elseif ($_GET['msg'] === 'need_login'): ?>
                    <div class="alert alert-warning py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                        <i class="bi bi-shield-lock-fill me-2 fs-6"></i>
                        <span>Silakan login terlebih dahulu untuk mengakses sistem.</span>
                    </div>
                <?php elseif ($_GET['msg'] === 'logged_out'): ?>
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                        <span>Anda telah berhasil logout dari sistem.</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <!-- Username / Email Input -->
                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold text-dark">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username" class="form-control border-start-0 ps-0" 
                               placeholder="Masukkan username atau email" required autofocus
                               value="<?= isset($_GET['user']) ? htmlspecialchars($_GET['user']) : (isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '') ?>">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold text-dark">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" 
                               placeholder="Masukkan password Anda" required>
                        <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-sky mt-4">
                    <span>Masuk ke Dashboard (Sign In)</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <!-- Bagian Pendaftaran Akun Baru (Sign Up) -->
            <div class="mt-4 pt-3 border-top text-center">
                <span class="text-muted small d-block mb-2">Belum memiliki akun terdaftar?</span>
                <a href="register.php" class="btn-signup-outline">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Daftar Akun Baru (Sign Up)</span>
                </a>
            </div>

            <div class="text-center mt-3 text-muted" style="font-size: 0.75rem;">
                <i class="bi bi-shield-check text-success me-1"></i> Keamanan sesi aktif &bull; Login terverifikasi database
            </div>
        </div>
    </div>

    <!-- Script toggle password visibility -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
