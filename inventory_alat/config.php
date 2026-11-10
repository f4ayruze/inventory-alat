<?php
// ==============================================================
// KONFIGURASI DATABASE SIMRS
// ==============================================================
// Jika di Localhost (XAMPP):
//   Host: localhost | User: root | Pass: (kosong) | DB: simrs
//
// Jika di Hosting Online (misal: InfinityFree):
//   Ganti 4 variabel di bawah ini sesuai informasi MySQL di Control Panel Hosting Anda.
// ==============================================================

$databaseHost     = getenv('DB_HOST') ?: 'localhost';
$databaseName     = getenv('DB_NAME') ?: 'simrs';
$databaseUsername = getenv('DB_USER') ?: 'root';
$databasePassword = getenv('DB_PASS') ?: '';

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if (!$mysqli) {
    die("<div style='font-family:sans-serif; padding:30px; text-align:center;'>
            <h3 style='color:#ef4444;'>Koneksi Database Gagal!</h3>
            <p>" . mysqli_connect_error() . "</p>
            <p style='color:#64748b;'>Jika Anda baru mengupload ke hosting, pastikan username, password, dan nama database di <code>config.php</code> sudah disesuaikan dengan akun MySQL hosting Anda.</p>
         </div>");
}
?>