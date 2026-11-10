<?php
include_once("config.php");
include_once("auth_check.php");

$errors = [];

// Cek ID
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: index.php");
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

// Proses Update
if (isset($_POST['update'])) {
    $nama_alat = trim($_POST['nama_alat']);
    $tahun     = intval($_POST['tahun']);
    $merek     = trim($_POST['merek']);
    $lokasi    = trim($_POST['lokasi']);

    if (empty($nama_alat)) $errors[] = "Nama alat wajib diisi.";
    if ($tahun < 1900 || $tahun > 2100) $errors[] = "Tahun tidak valid.";
    if (empty($merek)) $errors[] = "Merek wajib diisi.";
    if (empty($lokasi)) $errors[] = "Lokasi ruangan wajib diisi.";

    if (empty($errors)) {
        $nama_alat_esc = mysqli_real_escape_string($mysqli, $nama_alat);
        $merek_esc     = mysqli_real_escape_string($mysqli, $merek);
        $lokasi_esc    = mysqli_real_escape_string($mysqli, $lokasi);

        $query = "UPDATE alat SET nama_alat = '$nama_alat_esc', tahun = '$tahun', merek = '$merek_esc', lokasi = '$lokasi_esc' WHERE id = $id";
        if (mysqli_query($mysqli, $query)) {
            // Catat log
            catat_log($mysqli, "Mengubah data alat: $nama_alat (ID #$id, Merek: $merek, Lokasi: $lokasi)");

            header("Location: index.php?msg=updated");
            exit();
        } else {
            $errors[] = "Gagal memperbarui data: " . mysqli_error($mysqli);
        }
    }
}

// Ambil Data Saat Ini
$result = mysqli_query($mysqli, "SELECT * FROM alat WHERE id = $id");
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$alat = mysqli_fetch_assoc($result);

$pageTitle = "Edit Alat Medis #" . $id . " - SIMRS";
$activeMenu = "index";

include("header.php");
?>

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Edit Data Alat Medis</h1>
    <p class="page-subtitle mb-0">Perbarui spesifikasi dan informasi penempatan alat medis.</p>
  </div>
  <nav aria-label="breadcrumb" class="mt-2 mt-md-0">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Inventory</a></li>
      <li class="breadcrumb-item active text-primary" aria-current="page">Edit #<?= $id ?></li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 16px; background: #ffffff;">
      <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
        <div>
          <h5 class="card-title mb-1 fw-bold text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i>Perbarui Informasi Alat
          </h5>
          <small class="text-muted">Kode ID: <strong class="text-primary">#ALT-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></strong></small>
        </div>
        <a href="index.php" class="btn-custom btn-custom-sm btn-custom-secondary">
          <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="edit.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>">

        <!-- Nama Alat -->
        <div class="mb-3">
          <label for="nama_alat" class="form-label-custom fw-semibold">Nama Alat Medis <span class="text-danger">*</span></label>
          <input type="text" class="form-control-custom" id="nama_alat" name="nama_alat" 
                 value="<?= htmlspecialchars($alat['nama_alat']) ?>" required>
          <small class="text-muted">Nama peralatan elektromedis.</small>
        </div>

        <div class="row g-3">
          <!-- Tahun -->
          <div class="col-12 col-md-6 mb-3">
            <label for="tahun" class="form-label-custom fw-semibold">Tahun Pengadaan <span class="text-danger">*</span></label>
            <input type="number" class="form-control-custom" id="tahun" name="tahun" 
                   min="1990" max="2099" 
                   value="<?= htmlspecialchars($alat['tahun']) ?>" required>
            <small class="text-muted">Tahun perolehan / pembuatan.</small>
          </div>

          <!-- Merek -->
          <div class="col-12 col-md-6 mb-3">
            <label for="merek" class="form-label-custom fw-semibold">Merek / Pabrikan <span class="text-danger">*</span></label>
            <input type="text" class="form-control-custom" id="merek" name="merek" 
                   value="<?= htmlspecialchars($alat['merek']) ?>" required>
            <small class="text-muted">Pabrikan / brand pembuat.</small>
          </div>
        </div>

        <!-- Lokasi / Ruangan -->
        <div class="mb-4">
          <label for="lokasi" class="form-label-custom fw-semibold">Lokasi / Ruangan <span class="text-danger">*</span></label>
          <input type="text" class="form-control-custom" id="lokasi" name="lokasi" list="ruang-list"
                 value="<?= htmlspecialchars($alat['lokasi']) ?>" required maxlength="10">
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
          </datalist>
          <small class="text-muted">Maksimal 10 karakter (Contoh: ICU, OK, UGD, POLI).</small>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex align-items-center gap-2 pt-3 border-top">
          <button type="submit" name="update" class="btn-custom btn-custom-primary">
            <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
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
