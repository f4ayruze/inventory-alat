<?php
include_once("config.php");
include_once("auth_check.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil info nama alat sebelum dihapus untuk pencatatan log
    $q = mysqli_query($mysqli, "SELECT nama_alat FROM alat WHERE id = $id");
    $nama = ($q && $r = mysqli_fetch_assoc($q)) ? $r['nama_alat'] : "ID #$id";

    if (mysqli_query($mysqli, "DELETE FROM alat WHERE id = $id")) {
        catat_log($mysqli, "Menghapus data alat medis: $nama (ID #$id)");
        header("Location: index.php?msg=deleted");
        exit();
    } else {
        header("Location: index.php?msg=error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>