<?php
include_once("config.php");

$success = '';
$errors  = [];

if (isset($_POST['Submit'])) {
    // Validasi CSRF (Finding #5)
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token keamanan tidak valid. Muat ulang halaman dan coba lagi.";
    } else {
        $nama_alat = trim($_POST['nama_alat'] ?? '');
        $tahun     = intval($_POST['tahun'] ?? 0);
        $merek     = trim($_POST['merek'] ?? '');
        $lokasi    = trim($_POST['lokasi'] ?? '');

        if (empty($nama_alat)) $errors[] = "Nama alat tidak boleh kosong.";
        if ($tahun < 1900 || $tahun > 2099) $errors[] = "Tahun tidak valid.";
        if (empty($merek))    $errors[] = "Merek tidak boleh kosong.";
        if (empty($lokasi))   $errors[] = "Lokasi tidak boleh kosong.";

        if (empty($errors)) {
            // Prepared Statement mencegah SQL Injection (Finding #2)
            $stmt = mysqli_prepare($mysqli, "INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "siss", $nama_alat, $tahun, $merek, $lokasi);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Data berhasil ditambahkan!";
            } else {
                $errors[] = "Gagal menyimpan data ke database.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Alat - SIMRS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f8ff; }
        h2 { color: #0284C7; }
        table { width: 45%; border-collapse: collapse; }
        td { padding: 6px; }
        input[type=text], input[type=number] { width: 100%; padding: 6px; border: 1px solid #bcd; border-radius: 4px; }
        input[type=submit] { background: #0284C7; color: #fff; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 8px 14px; border-radius: 4px; margin-bottom: 10px; }
        .alert-error   { background: #fee2e2; color: #991b1b; padding: 8px 14px; border-radius: 4px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Tambah Data Alat Medis</h2>
    <a href="index.php">Kembali ke Beranda</a><br><br>

    <?php if (!empty($success)): ?>
        <div class="alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            <a href="index.php">Lihat Data</a>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="add.php" method="post" name="form1">
        <!-- Token CSRF (Finding #5) -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <table>
            <tr>
                <td>Nama Alat</td>
                <td><input type="text" name="nama_alat" maxlength="100" required></td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td><input type="number" name="tahun" min="1900" max="2099" value="<?= date('Y') ?>" required></td>
            </tr>
            <tr>
                <td>Merek</td>
                <td><input type="text" name="merek" maxlength="50" required></td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td><input type="text" name="lokasi" maxlength="50" required></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="Submit" value="Simpan"></td>
            </tr>
        </table>
    </form>
</body>
</html>
