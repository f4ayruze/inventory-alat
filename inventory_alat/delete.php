<?php
include_once("config.php");
include_once("auth_check.php");

// Keamanan: Hanya method POST yang diizinkan untuk menghapus data (mencegah CSRF via GET link / img tag)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?msg=error");
    exit();
}

// Validasi Token CSRF
$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    header("Location: index.php?msg=csrf_error");
    exit();
}

// Keamanan RBAC: Hanya Super Admin yang berhak menghapus data alat
require_role('Super Admin');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id > 0) {
    // Ambil info nama alat sebelum dihapus untuk pencatatan audit log
    $qStmt = mysqli_prepare($mysqli, "SELECT nama_alat FROM alat WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($qStmt, "i", $id);
    mysqli_stmt_execute($qStmt);
    $qRes = mysqli_stmt_get_result($qStmt);
    $nama = ($qRes && $r = mysqli_fetch_assoc($qRes)) ? $r['nama_alat'] : "ID #$id";
    mysqli_stmt_close($qStmt);

    // Hapus data dengan Prepared Statement
    $delStmt = mysqli_prepare($mysqli, "DELETE FROM alat WHERE id = ?");
    mysqli_stmt_bind_param($delStmt, "i", $id);
    
    if (mysqli_stmt_execute($delStmt)) {
        mysqli_stmt_close($delStmt);
        catat_log($mysqli, "Menghapus data alat medis: $nama (ID #$id)");
        header("Location: index.php?msg=deleted");
        exit();
    } else {
        mysqli_stmt_close($delStmt);
        header("Location: index.php?msg=error");
        exit();
    }
} else {
    header("Location: index.php?msg=error");
    exit();
}
?>