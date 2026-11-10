<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");

if (isset($_SESSION['user_id'])) {
    $user_id  = intval($_SESSION['user_id']);
    $username = mysqli_real_escape_string($mysqli, $_SESSION['username']);
    $nama     = mysqli_real_escape_string($mysqli, $_SESSION['nama_lengkap']);
    $ip       = mysqli_real_escape_string($mysqli, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    $ua       = mysqli_real_escape_string($mysqli, substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255));

    $sqlLog = "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) 
               VALUES ($user_id, '$username', '$nama', 'Logout dari Sistem', '$ip', '$ua', NOW())";
    mysqli_query($mysqli, $sqlLog);
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