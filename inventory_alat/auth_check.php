<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7200, // 2 jam sesi aktif
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Inisialisasi token CSRF jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Cek autentikasi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=need_login");
    exit();
}

/**
 * Validasi Token CSRF untuk keamanan form POST
 */
function validate_csrf($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verifikasi Hak Akses Role (RBAC)
 */
function require_role($allowed_roles = ['Super Admin']) {
    $current_role = $_SESSION['role'] ?? '';
    if (!in_array($current_role, (array)$allowed_roles)) {
        header("Location: index.php?msg=unauthorized");
        exit();
    }
}

/**
 * Fungsi pencatat audit log ke tabel simrs.log_akses dengan Prepared Statement
 */
function catat_log($mysqli, $aktivitas) {
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $username = $_SESSION['username'] ?? 'Tamu';
    $nama_lengkap = $_SESSION['nama_lengkap'] ?? 'Tamu Sistem';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

    $stmt = mysqli_prepare($mysqli, "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssss", $user_id, $username, $nama_lengkap, $aktivitas, $ip, $ua);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>