<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek autentikasi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=need_login");
    exit();
}

/**
 * Fungsi pembantu untuk mencatat aktivitas dan riwayat akses ke tabel log_akses
 */
function catat_log($mysqli, $aktivitas) {
    if (isset($_SESSION['user_id'])) {
        $user_id = intval($_SESSION['user_id']);
        $username = mysqli_real_escape_string($mysqli, $_SESSION['username']);
        $nama_lengkap = mysqli_real_escape_string($mysqli, $_SESSION['nama_lengkap']);
    } else {
        $user_id = "NULL";
        $username = "Tamu";
        $nama_lengkap = "Tamu Sistem";
    }

    $aktivitas_esc = mysqli_real_escape_string($mysqli, $aktivitas);
    $ip = mysqli_real_escape_string($mysqli, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    $ua = mysqli_real_escape_string($mysqli, substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255));

    $sql = "INSERT INTO log_akses (user_id, username, nama_lengkap, aktivitas, ip_address, user_agent, waktu) 
            VALUES ($user_id, '$username', '$nama_lengkap', '$aktivitas_esc', '$ip', '$ua', NOW())";
    mysqli_query($mysqli, $sql);
}
?>