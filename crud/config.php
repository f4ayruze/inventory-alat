<?php
$databaseHost = 'localhost';
$databaseName = 'simrs';
$databaseUsername = 'root';
$databasePassword = '';

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if (!$mysqli) {
    die("Koneksi database gagal.");
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7200, // 2 jam sesi aktif
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Inisialisasi token CSRF jika belum ada (Finding #5)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Cek autentikasi login (Finding #2): Pengunjung wajib login sebelum mengakses CRUD
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inventory_alat/login.php?msg=need_login");
    exit();
}

/**
 * Validasi Token CSRF
 */
function validate_csrf($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>
