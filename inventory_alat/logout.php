<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");

if (isset($_SESSION['user_id'])) {
    $user_id  = intval($_SESSION['user_id']);
    $username = $_SESSION['username'] ?? 'User';
    $nama     = $_SESSION['nama_lengkap'] ?? 'User';
    $ip       = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ua       = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

    $stmt = mysqli_prepare($mysqli, "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) VALUES (?, ?, ?, 'Logout dari Sistem', ?, ?, NOW())");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $username, $nama, $ip, $ua);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Hapus semua data sesi
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: login.php?msg=logged_out");
exit();
?>
