<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");

// Jika sudah login, langsung ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Silakan masukkan username dan password!";
    } else {
        $username_esc = mysqli_real_escape_string($mysqli, $username);
        $query = "SELECT * FROM users WHERE username = '$username_esc' LIMIT 1";
        $result = mysqli_query($mysqli, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Validasi password (MD5, password_verify, atau plain text)
            if ($user['password'] === md5($password) || password_verify($password, $user['password']) || $user['password'] === $password) {
                // Login Berhasil
                $_SESSION['user_id']      = $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role']         = $user['role'];

                // Update terakhir login
                mysqli_query($mysqli, "UPDATE users SET terakhir_login = NOW() WHERE id = " . $user['id']);

                // Catat ke log_akses
                $ip = mysqli_real_escape_string($mysqli, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                $ua = mysqli_real_escape_string($mysqli, substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255));
                $nama_esc = mysqli_real_escape_string($mysqli, $user['nama_lengkap']);
                $u_esc    = mysqli_real_escape_string($mysqli, $user['username']);

                $sqlLog = "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) 
                           VALUES ({$user['id']}, '$u_esc', '$nama_esc', 'Login Berhasil ke Sistem', '$ip', '$ua', NOW())";
                mysqli_query($mysqli, $sqlLog);

                header("Location: index.php");
                exit();
            } else {
                // Catat percobaan gagal
                $ip = mysqli_real_escape_string($mysqli, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                $ua = mysqli_real_escape_string($mysqli, substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255));
                mysqli_query($mysqli, "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) 
                                       VALUES (NULL, '$username_esc', 'Percobaan Gagal', 'Gagal Login: Password Salah', '$ip', '$ua', NOW())");
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak terdaftar dalam database!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMRS Inventory Alat Medis</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">
    
    <!-- Libraries -->
    <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">
    
    <!-- Main Design System (Sky Blue Theme) -->
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
            margin-bottom: 0.5rem;
        }
        .login-brand i {
            color: #0284C7;
            font-size: 1.8rem;
        }
        .login-subtitle {
            text-align: center;
            color: #64748B;
            font-size: 0.9rem;
            margin-bottom: 1.75rem;
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
        .account-chip {
            background: #F1F5F9;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.78rem;
            cursor: pointer;
            color: #0F172A;
            transition: all 0.15s ease;
        }
        .account-chip:hover {
            background: #E0F2FE;
            border-color: #BAE6FD;
            color: #0284C7;
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
            <p class="login-subtitle">Sistem Inventory Peralatan Medis Rumah Sakit</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'need_login'): ?>
                    <div class="alert alert-warning py-2 px-3 small d-flex align-items-center" role="alert">
                        <i class="bi bi-shield-lock-fill me-2 fs-6"></i>
                        <span>Silakan login terlebih dahulu untuk mengakses sistem.</span>
                    </div>
                <?php elseif ($_GET['msg'] === 'logged_out'): ?>
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                        <span>Anda telah berhasil logout dari sistem.</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <!-- Username Input -->
                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold text-dark">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username" class="form-control border-start-0 ps-0" 
                               placeholder="Masukkan username" required autofocus
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold text-dark">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" 
                               placeholder="Masukkan password" required>
                        <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-sky mt-4">
                    <span>Masuk ke Dashboard</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <!-- Info Akun Tersedia -->
            <div class="mt-4 pt-3 border-top">
                <div class="small fw-semibold text-muted mb-2 text-center">Akun Terdaftar di Database (Klik untuk Isi):</div>
                <div class="d-flex flex-column gap-1">
                    <div class="account-chip d-flex justify-content-between align-items-center" onclick="setAccount('admin', 'admin123')">
                        <span><strong>admin</strong> <small class="text-muted">(Super Admin)</small></span>
                        <code class="text-secondary small">admin123</code>
                    </div>
                    <div class="account-chip d-flex justify-content-between align-items-center" onclick="setAccount('syuja', '1202405030')">
                        <span><strong>syuja</strong> <small class="text-muted">(Teknisi Elektromedik)</small></span>
                        <code class="text-secondary small">1202405030</code>
                    </div>
                    <div class="account-chip d-flex justify-content-between align-items-center" onclick="setAccount('elektromedik', '123456')">
                        <span><strong>elektromedik</strong> <small class="text-muted">(Staf RS)</small></span>
                        <code class="text-secondary small">123456</code>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3 text-muted" style="font-size: 0.75rem;">
                Setiap akses login tercatat otomatis ke database <code>simrs.log_akses</code>
            </div>
        </div>
    </div>

    <!-- Script toggle password & auto-fill -->
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

        function setAccount(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
            document.getElementById('username').focus();
        }
    </script>
</body>
</html>
