<?php
include_once("config.php");
include_once("auth_check.php");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validasi Token CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($token)) {
        $errors[] = "Token keamanan CSRF tidak valid atau telah kedaluwarsa. Silakan refresh halaman.";
    } else {
        $nama_alat = trim($_POST['nama_alat'] ?? '');
        $tahun     = intval($_POST['tahun'] ?? 0);
        $merek     = trim($_POST['merek'] ?? '');
        $lokasi    = trim($_POST['lokasi'] ?? '');

        if (empty($nama_alat)) $errors[] = "Nama alat medis wajib diisi.";
        if ($tahun < 1900 || $tahun > 2100) $errors[] = "Tahun pengadaan tidak valid.";
        if (empty($merek)) $errors[] = "Merek atau pabrikan wajib diisi.";
        if (empty($lokasi)) $errors[] = "Lokasi ruangan wajib diisi.";
        if (strlen($lokasi) > 50) $errors[] = "Nama ruangan maksimal 50 karakter.";

        if (empty($errors)) {
            // Pencegahan data duplikat (cek alat dengan nama dan lokasi yang sama)
            $dupStmt = mysqli_prepare($mysqli, "SELECT id FROM alat WHERE LOWER(nama_alat) = LOWER(?) AND LOWER(lokasi) = LOWER(?) LIMIT 1");
            mysqli_stmt_bind_param($dupStmt, "ss", $nama_alat, $lokasi);
            mysqli_stmt_execute($dupStmt);
            $dupRes = mysqli_stmt_get_result($dupStmt);

            if ($dupRes && mysqli_num_rows($dupRes) > 0) {
                $errors[] = "Alat medis dengan nama '" . htmlspecialchars($nama_alat, ENT_QUOTES, 'UTF-8') . "' sudah terdaftar di ruangan '" . htmlspecialchars($lokasi, ENT_QUOTES, 'UTF-8') . "'.";
                mysqli_stmt_close($dupStmt);
            } else {
                mysqli_stmt_close($dupStmt);

                // Insert dengan Prepared Statement (Anti SQL Injection)
                $insertStmt = mysqli_prepare($mysqli, "INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($insertStmt, "siss", $nama_alat, $tahun, $merek, $lokasi);

                if (mysqli_stmt_execute($insertStmt)) {
                    mysqli_stmt_close($insertStmt);
                    catat_log($mysqli, "Menambah alat medis baru: $nama_alat ($merek, Lokasi: $lokasi)");
                    header("Location: index.php?msg=added");
                    exit();
                } else {
                    $errors[] = "Terjadi kesalahan saat menyimpan ke database.";
                    mysqli_stmt_close($insertStmt);
                }
            }
        }
    }
}

$pageTitle = "Tambah Alat Medis - SIMRS";
$activeMenu = "add";

include("header.php");
?>

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Tambah Alat Medis</h1>
    <p class="page-subtitle mb-0">Tambahkan catatan aset alat medis baru ke database SIMRS.</p>
  </div>
  <nav aria-label="breadcrumb" class="mt-2 mt-md-0">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Inventory</a></li>
      <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Alat</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 16px; background: #ffffff;">
      <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
        <h5 class="card-title mb-0 fw-bold text-dark">
          <i class="bi bi-plus-square-fill text-primary me-2"></i>Formulir Data Alat
        </h5>
        <a href="index.php" class="btn-custom btn-custom-sm btn-custom-secondary">
          <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="add.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Nama Alat -->
        <div class="mb-3">
          <label for="nama_alat" class="form-label-custom fw-semibold">Nama Alat Medis <span class="text-danger">*</span></label>
          <input type="text" class="form-control-custom" id="nama_alat" name="nama_alat" 
                 placeholder="Contoh: Suction Pump Portable, Defibrillator, ECG" 
                 value="<?= isset($_POST['nama_alat']) ? htmlspecialchars($_POST['nama_alat'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
          <small class="text-muted">Masukkan nama resmi peralatan elektromedis.</small>
        </div>

        <div class="row g-3">
          <!-- Tahun -->
          <div class="col-12 col-md-6 mb-3">
            <label for="tahun" class="form-label-custom fw-semibold">Tahun Pengadaan <span class="text-danger">*</span></label>
            <input type="number" class="form-control-custom" id="tahun" name="tahun" 
                   min="1990" max="2099" 
                   placeholder="Contoh: <?= date('Y') ?>" 
                   value="<?= isset($_POST['tahun']) ? htmlspecialchars($_POST['tahun'], ENT_QUOTES, 'UTF-8') : date('Y') ?>" required>
            <small class="text-muted">Tahun pembuatan atau perolehan alat.</small>
          </div>

          <!-- Merek -->
          <div class="col-12 col-md-6 mb-3">
            <label for="merek" class="form-label-custom fw-semibold">Merek / Pabrikan <span class="text-danger">*</span></label>
            <input type="text" class="form-control-custom" id="merek" name="merek" 
                   placeholder="Contoh: GEA Medical, Mindray, Terumo" 
                   value="<?= isset($_POST['merek']) ? htmlspecialchars($_POST['merek'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
            <small class="text-muted">Brand pembuat perangkat.</small>
          </div>
        </div>

        <!-- Lokasi / Ruangan -->
        <div class="mb-4">
          <label for="lokasi" class="form-label-custom fw-semibold">Lokasi / Ruangan <span class="text-danger">*</span></label>
          <input type="text" class="form-control-custom" id="lokasi" name="lokasi" list="ruang-list"
                 placeholder="Contoh: OK, ICU, UGD, POLI, LAB, HEMODIALISA" 
                 value="<?= isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi'], ENT_QUOTES, 'UTF-8') : '' ?>" required maxlength="50">
          <datalist id="ruang-list">
            <option value="ICU">
            <option value="UGD">
            <option value="OK">
            <option value="POLI">
            <option value="LAB">
            <option value="RADIOLOGI">
            <option value="RAWAT INAP">
            <option value="CSSD">
            <option value="HEMODIALISA">
            <option value="NEONATOLOGI">
          </datalist>
          <small class="text-muted">Maksimal 50 karakter (Daftar rekomendasi: ICU, OK, UGD, HEMODIALISA, dll.).</small>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex align-items-center gap-2 pt-3 border-top">
          <button type="submit" name="submit" class="btn-custom btn-custom-primary">
            <i class="bi bi-save me-1"></i> Simpan Data Alat
          </button>
          <a href="index.php" class="btn-custom btn-custom-light">
            Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
