<?php
include_once("config.php");

// Handle POST update (Finding #2 & #7)
if (isset($_POST['update'])) {
    // Validasi CSRF (Finding #5)
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        die("Token keamanan tidak valid. Silakan kembali dan coba lagi.");
    }

    $id        = intval($_POST['id'] ?? 0);
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $tahun     = intval($_POST['tahun'] ?? 0);
    $merek     = trim($_POST['merek'] ?? '');
    $lokasi    = trim($_POST['lokasi'] ?? '');

    if ($id > 0 && !empty($nama_alat) && !empty($merek) && !empty($lokasi)) {
        // Prepared Statement mencegah SQL Injection (Finding #2)
        $stmt = mysqli_prepare($mysqli, "UPDATE alat SET nama_alat=?, tahun=?, merek=?, lokasi=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sissi", $nama_alat, $tahun, $merek, $lokasi, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: index.php");
    exit();
}

// Ambil data berdasarkan ID yang divalidasi (Finding #7: tidak lagi echo $_GET['id'] mentah)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$stmt = mysqli_prepare($mysqli, "SELECT * FROM alat WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    header("Location: index.php");
    exit();
}

$nama_alat = $row['nama_alat'];
$tahun     = $row['tahun'];
$merek     = $row['merek'];
$lokasi    = $row['lokasi'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Alat - SIMRS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f8ff; }
        h2 { color: #0284C7; }
        table { width: 45%; border-collapse: collapse; }
        td { padding: 6px; }
        input[type=text], input[type=number] { width: 100%; padding: 6px; border: 1px solid #bcd; border-radius: 4px; }
        input[type=submit] { background: #0284C7; color: #fff; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Edit Data Alat Medis</h2>
    <a href="index.php">Kembali</a><br><br>

    <form name="update_alat" method="post" action="edit.php">
        <!-- Token CSRF (Finding #5) -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <!-- ID menggunakan intval, tidak dicetak mentah (Finding #7) -->
        <input type="hidden" name="id" value="<?= intval($id) ?>">
        <table>
            <tr>
                <td>Nama Alat</td>
                <td><input type="text" name="nama_alat" maxlength="100" value="<?= htmlspecialchars($nama_alat, ENT_QUOTES, 'UTF-8') ?>" required></td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td><input type="number" name="tahun" min="1900" max="2099" value="<?= intval($tahun) ?>"></td>
            </tr>
            <tr>
                <td>Merek</td>
                <td><input type="text" name="merek" maxlength="50" value="<?= htmlspecialchars($merek, ENT_QUOTES, 'UTF-8') ?>"></td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td><input type="text" name="lokasi" maxlength="50" value="<?= htmlspecialchars($lokasi, ENT_QUOTES, 'UTF-8') ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>
    </form>
</body>
</html>
