<?php
include_once("config.php");
include_once("auth_check.php");

$pageTitle = "Daftar Alat Medis - SIMRS";
$activeMenu = "index";

// Hitung Statistik
$statTotal = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS cnt FROM alat"))['cnt'];
$statMerek = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT merek) AS cnt FROM alat"))['cnt'];
$statLokasi = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT lokasi) AS cnt FROM alat"))['cnt'];

// Fitur Filter Lokasi & Pencarian
$lokasiFilter = isset($_GET['lokasi']) ? trim($_GET['lokasi']) : '';
$search       = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereClauses = [];
if (!empty($search)) {
    $searchEscaped = mysqli_real_escape_string($mysqli, $search);
    $whereClauses[] = "(nama_alat LIKE '%$searchEscaped%' OR merek LIKE '%$searchEscaped%' OR lokasi LIKE '%$searchEscaped%' OR tahun LIKE '%$searchEscaped%')";
}
if (!empty($lokasiFilter)) {
    $lokasiEscaped = mysqli_real_escape_string($mysqli, $lokasiFilter);
    $whereClauses[] = "lokasi = '$lokasiEscaped'";
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

// Pagination logic (Finding #12)
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalFilteredRes = mysqli_query($mysqli, "SELECT COUNT(*) AS cnt FROM alat $whereSql");
$totalFiltered = mysqli_fetch_assoc($totalFilteredRes)['cnt'];
$totalPages = max(1, (int)ceil($totalFiltered / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$query = "SELECT * FROM alat $whereSql ORDER BY id DESC LIMIT $perPage OFFSET $offset";
$result = mysqli_query($mysqli, $query);

// Ambil daftar lokasi unik untuk filter pills
$lokasiQuery = mysqli_query($mysqli, "SELECT DISTINCT lokasi FROM alat WHERE lokasi != '' ORDER BY lokasi ASC");

$is_super_admin = ($_SESSION['role'] ?? '') === 'Super Admin';

include("header.php");
?>

<!-- START: Dashboard Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Inventory Peralatan Medis</h1>
    <p class="page-subtitle mb-0">Sistem Informasi Manajemen Rumah Sakit (SIMRS) • Manajemen Pemeliharaan & Aset Elektromedik</p>
  </div>
  <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
    <button type="button" class="btn-custom btn-custom-secondary" data-bs-toggle="modal" data-bs-target="#modalTambahAlat">
      <i class="bi bi-lightning-charge-fill"></i>
      <span>+ Tambah Cepat</span>
    </button>
    <a href="add.php" class="btn-custom btn-custom-primary">
      <i class="bi bi-plus-lg"></i>
      <span>Form Input Lengkap</span>
    </a>
  </div>
</div>
<!-- END: Dashboard Header Banner -->

<!-- START: Alert Info Banner (Biru Langit) -->
<div class="card alert-green-card p-3 mb-4 border-0">
  <div class="d-flex flex-wrap justify-content-between align-items-center position-relative z-index-2">
    <div>
      <span class="alert-green-badge"><i class="bi bi-check-circle-fill me-1"></i> SIMRS Online</span>
      <span class="ms-2 fw-bold text-white">Database: `simrs` &bull; Login sebagai: <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($_SESSION['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</span>
      <div class="text-white-50 small mt-1">Sistem inventaris alat medis aktif. Seluruh riwayat akses dicatat untuk keamanan data aset rumah sakit.</div>
    </div>
    <div class="mt-2 mt-sm-0 d-flex gap-2">
      <?php if ($is_super_admin): ?>
      <a href="log_akses.php" class="btn btn-sm btn-light text-primary fw-semibold px-3 py-1 rounded-pill">
        <i class="bi bi-shield-check me-1"></i> Cek Log Akses Sistem
      </a>
      <?php endif; ?>
      <a href="add.php" class="alert-green-link text-decoration-none d-flex align-items-center">
        <span>+ Entri Alat Baru</span>
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</div>
<!-- END: Alert Info Banner -->

<!-- START: Stat Cards Row -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 16px; background: #ffffff;">
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; border-radius: 12px; background: #E0F2FE; color: #0284C7;">
          <i class="bi bi-heart-pulse-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small">Total Alat Terdata</div>
          <div class="fs-4 fw-bold text-dark"><?= number_format($statTotal) ?> <span class="fs-6 fw-normal text-muted">Unit</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-4">
    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 16px; background: #ffffff;">
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; border-radius: 12px; background: #E0F2FE; color: #0284C7;">
          <i class="bi bi-tag-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small">Merek Terdaftar</div>
          <div class="fs-4 fw-bold text-dark"><?= number_format($statMerek) ?> <span class="fs-6 fw-normal text-muted">Produsen</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-4">
    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 16px; background: #ffffff;">
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; border-radius: 12px; background: #E0F2FE; color: #0284C7;">
          <i class="bi bi-geo-alt-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small">Sebaran Ruangan</div>
          <div class="fs-4 fw-bold text-dark"><?= number_format($statLokasi) ?> <span class="fs-6 fw-normal text-muted">Unit Kerja</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END: Stat Cards Row -->

<!-- START: Filter By Location Tabs -->
<?php if ($lokasiQuery && mysqli_num_rows($lokasiQuery) > 0): ?>
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <span class="text-muted small fw-semibold me-1"><i class="bi bi-funnel"></i> Ruangan:</span>
    <a href="index.php" class="btn btn-sm <?= empty($lokasiFilter) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3" style="font-size: 0.78rem;">
      Semua Ruangan
    </a>
    <?php while ($loc = mysqli_fetch_assoc($lokasiQuery)): ?>
      <a href="index.php?lokasi=<?= urlencode($loc['lokasi']) ?>" 
         class="btn btn-sm <?= ($lokasiFilter === $loc['lokasi']) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3" 
         style="font-size: 0.78rem;">
        <?= htmlspecialchars($loc['lokasi'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endwhile; ?>
  </div>
<?php endif; ?>
<!-- END: Filter By Location Tabs -->

<!-- START: Basic Table Card Container -->
<div class="table-card-custom">
  <!-- Header Controls -->
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center gap-2">
    <!-- Search bar -->
    <form action="index.php" method="get" class="table-search-box" style="min-width: 300px;">
      <?php if (!empty($lokasiFilter)): ?>
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasiFilter, ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>
      <i class="bi bi-search table-search-icon"></i>
      <input type="text" name="search" class="table-search-input" placeholder="Cari nama alat, merek, atau lokasi..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
    </form>

    <div class="d-flex align-items-center gap-2">
      <?php if (!empty($search) || !empty($lokasiFilter)): ?>
        <a href="index.php" class="btn-custom btn-custom-sm btn-custom-light">
          <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </a>
      <?php endif; ?>
      <button type="button" class="btn-custom btn-custom-sm btn-custom-secondary" data-bs-toggle="modal" data-bs-target="#modalTambahAlat">
        <i class="bi bi-plus-lg"></i> Tambah Cepat
      </button>
      <a href="add.php" class="btn-custom btn-custom-sm btn-custom-primary">
        <i class="bi bi-file-earmark-plus"></i> Form Lengkap
      </a>
    </div>
  </div>

  <!-- Responsive Table Wrapper -->
  <div class="table-responsive">
    <table class="table-custom mb-0">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Kode Alat</th>
          <th>Nama Alat Medis</th>
          <th>Tahun</th>
          <th>Merek / Pabrikan</th>
          <th>Ruang / Lokasi</th>
          <th>Status</th>
          <th class="text-center" style="width: 130px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <?php 
          $no = $offset + 1;
          while ($row = mysqli_fetch_assoc($result)): 
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td class="table-order-id">#ALT-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="d-flex align-items-center justify-content-center rounded" style="width: 36px; height: 36px; background: #E0F2FE; color: #0284C7;">
                    <i class="bi bi-tools"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8') ?></div>
                    <small class="text-muted">ID: <?= intval($row['id']) ?></small>
                  </div>
                </div>
              </td>
              <td><span class="badge-tahun"><?= htmlspecialchars($row['tahun'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><span class="fw-medium text-dark"><?= htmlspecialchars($row['merek'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td>
                <span class="badge-lokasi">
                  <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($row['lokasi'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td>
                <span class="status-pill-active">
                  <span class="status-dot-green"></span> Siap Pakai
                </span>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-1 align-items-center">
                  <a href="edit.php?id=<?= intval($row['id']) ?>" class="table-btn-action" title="Edit Data Alat">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <?php if ($is_super_admin): ?>
                  <!-- Form Hapus Aman: Method POST + CSRF Token + Anti Stored-XSS (Finding #3 & #5) -->
                  <form action="delete.php" method="POST" class="d-inline m-0 p-0" onsubmit="return confirmDelete(this);">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= intval($row['id']) ?>">
                    <input type="hidden" name="item_name" value="<?= htmlspecialchars($row['nama_alat'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="table-btn-action delete border-0 bg-transparent p-0" title="Hapus Data Alat (Super Admin)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                  <?php else: ?>
                  <span class="table-btn-action text-muted" title="Hanya Super Admin yang dapat menghapus alat" style="opacity: 0.4; cursor: not-allowed;">
                    <i class="bi bi-trash"></i>
                  </span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center py-5">
              <div class="text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <h6 class="text-dark fw-bold mb-1">Belum Ada Data Alat Medis</h6>
                <p class="mb-3 small">Data alat belum ada atau tidak cocok dengan filter pencarian.</p>
                <button type="button" class="btn-custom btn-custom-sm btn-custom-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAlat">
                  <i class="bi bi-plus-lg me-1"></i> Tambah Data Sekarang
                </button>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Footer Controls & Pagination (Finding #12) -->
  <div class="table-footer-control d-flex flex-wrap justify-content-between align-items-center p-3 border-top gap-2">
    <span class="table-pagination-info text-muted small">
      Menampilkan <strong><?= ($result) ? mysqli_num_rows($result) : 0 ?></strong> dari <strong><?= $totalFiltered ?></strong> unit peralatan medis (Halaman <?= $page ?> dari <?= $totalPages ?>)
    </span>

    <?php if ($totalPages > 1): ?>
    <nav aria-label="Navigasi Halaman">
      <ul class="pagination pagination-sm mb-0">
        <?php
        $queryParams = $_GET;
        ?>
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
          <?php $queryParams['page'] = max(1, $page - 1); ?>
          <a class="page-link" href="?<?= http_build_query($queryParams) ?>" aria-label="Sebelumnya">
            &laquo; Prev
          </a>
        </li>

        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <?php $queryParams['page'] = $p; ?>
          <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query($queryParams) ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>

        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
          <?php $queryParams['page'] = min($totalPages, $page + 1); ?>
          <a class="page-link" href="?<?= http_build_query($queryParams) ?>" aria-label="Berikutnya">
            Next &raquo;
          </a>
        </li>
      </ul>
    </nav>
    <?php endif; ?>

    <span class="small text-muted">
      Database: <code class="text-dark">simrs</code> &bull; Tabel: <code class="text-dark">alat</code>
    </span>
  </div>
</div>
<!-- END: Basic Table Card Container -->

<!-- Modal Tambah Cepat Alat Medis -->
<div class="modal fade" id="modalTambahAlat" tabindex="-1" aria-labelledby="modalTambahAlatLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold text-dark" id="modalTambahAlatLabel">
          <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Alat Medis Cepat
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="add.php" method="post">
        <!-- Token CSRF (Finding #5) -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <div class="modal-body p-4">
          <!-- Nama Alat -->
          <div class="mb-3">
            <label class="form-label-custom fw-semibold">Nama Alat Medis <span class="text-danger">*</span></label>
            <input type="text" name="nama_alat" class="form-control-custom" placeholder="Contoh: Suction Pump Portable" required maxlength="100">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label-custom fw-semibold">Tahun <span class="text-danger">*</span></label>
              <input type="number" name="tahun" min="1990" max="2099" value="<?= date('Y') ?>" class="form-control-custom" required>
            </div>
            <div class="col-6">
              <label class="form-label-custom fw-semibold">Merek <span class="text-danger">*</span></label>
              <input type="text" name="merek" class="form-control-custom" placeholder="Contoh: GEA Medical" required maxlength="50">
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label-custom fw-semibold">Lokasi / Ruangan <span class="text-danger">*</span></label>
            <!-- Batas panjang 50 karakter & dukungan HEMODIALISA (Finding #10) -->
            <input type="text" name="lokasi" list="modal-ruang-list" class="form-control-custom" placeholder="Contoh: OK, ICU, UGD, HEMODIALISA" required maxlength="50">
            <datalist id="modal-ruang-list">
              <option value="ICU">
              <option value="UGD">
              <option value="OK">
              <option value="POLI">
              <option value="LAB">
              <option value="RADIOLOGI">
              <option value="RAWAT INAP">
              <option value="HEMODIALISA">
              <option value="FARMASI">
              <option value="CSSD">
            </datalist>
            <small class="text-muted">Maksimal 50 karakter.</small>
          </div>
        </div>
        <div class="modal-footer border-top p-3">
          <button type="button" class="btn-custom btn-custom-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="submit" class="btn-custom btn-custom-primary">
            <i class="bi bi-save me-1"></i> Simpan Data Alat
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JavaScript Konfirmasi Hapus Aman Bebas Stored XSS (Finding #3) -->
<script>
function confirmDelete(form) {
  var name = (form && form.item_name) ? form.item_name.value : 'alat ini';
  return confirm('Apakah Anda yakin ingin menghapus data alat: ' + name + '?');
}
</script>

<?php include("footer.php"); ?>
