<?php
include_once("config.php");

// Hanya method POST yang diizinkan (Finding #2 & #5)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Validasi CSRF (Finding #5)
if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    die("Token keamanan tidak valid. Silakan kembali dan coba lagi.");
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id > 0) {
    // Prepared Statement mencegah SQL Injection (Finding #2)
    $stmt = mysqli_prepare($mysqli, "DELETE FROM alat WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: index.php");
exit();
?>
