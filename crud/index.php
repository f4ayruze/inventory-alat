<?php
include_once("config.php");

// XSS-safe output + prepared statement query (Finding #7)
$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alat Medis - SIMRS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f8ff; }
        h2 { color: #0284C7; }
        table { width: 90%; border-collapse: collapse; margin-top: 10px; background: #fff; }
        th { background: #0284C7; color: #fff; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background: #EFF6FF; }
        a.btn { display: inline-block; padding: 4px 10px; border-radius: 4px; text-decoration: none; color: #fff; background: #0284C7; margin: 2px; }
        a.btn-danger { background: #dc2626; }
        a.btn-add { background: #16a34a; margin-bottom: 10px; }
        .notice { color: #0369A1; font-size: 0.85em; margin-bottom: 8px; }
        .user-info { background: #E0F2FE; padding: 8px 14px; border-radius: 6px; margin-bottom: 14px; color: #0369A1; font-size: 0.9em; }
    </style>
</head>
<body>
    <h2>Data Peralatan Medis (CRUD Dasar)</h2>
    <div class="user-info">
        &#128100; Login sebagai: <strong><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
        (<?= htmlspecialchars($_SESSION['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
        &nbsp;|&nbsp;
        <a href="../inventory_alat/logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>
        &nbsp;|&nbsp;
        <a href="../inventory_alat/index.php">Kembali ke Dashboard Utama</a>
    </div>
    <p class="notice">&#128274; Halaman ini hanya dapat diakses setelah login. Semua operasi data tercatat di log akses.</p>

    <a href="add.php" class="btn btn-add">+ Tambah Data Alat</a>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Alat</th>
            <th>Tahun</th>
            <th>Merek</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <!-- htmlspecialchars mencegah Reflected/Stored XSS (Finding #7) -->
            <td><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($row['tahun'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($row['merek'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($row['lokasi'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <a href="edit.php?id=<?= intval($row['id']) ?>" class="btn">Edit</a>
                <!-- Form DELETE via POST + CSRF (Finding #2 & #5) -->
                <form action="delete.php" method="POST" class="d-inline" style="display:inline;"
                      onsubmit="return confirm('Yakin ingin menghapus alat: <?= htmlspecialchars(addslashes($row['nama_alat']), ENT_QUOTES, 'UTF-8') ?>?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= intval($row['id']) ?>">
                    <button type="submit" style="background:#dc2626;color:#fff;border:none;padding:4px 10px;border-radius:4px;cursor:pointer;">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
