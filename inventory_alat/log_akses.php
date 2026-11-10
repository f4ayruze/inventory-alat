<?php
include_once("config.php");
include_once("auth_check.php");

$pageTitle = "Riwayat & Log Akses Pengguna - SIMRS";
$activeMenu = "log_akses";

// Pencarian log
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereSql = "";
if (!empty($search)) {
    $searchEsc = mysqli_real_escape_string($mysqli, $search);
    $whereSql = "WHERE username LIKE '%$searchEsc%' OR nama_lengkap LIKE '%$searchEsc%' OR aktivitas LIKE '%$searchEsc%' OR ip_address LIKE '%$searchEsc%'";
}

$query = "SELECT * FROM log_akses $whereSql ORDER BY waktu DESC LIMIT 100";
$result = mysqli_query($mysqli, $query);

// Hitung statistik log
$statTotalLog = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS cnt FROM log_akses"))['cnt'];
$statTodayLog = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS cnt FROM log_akses WHERE DATE(waktu) = CURDATE()"))['cnt'];
$statUniqueUser = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT username) AS cnt FROM log_akses WHERE username != ''"))['cnt'];

include("header.php");
?>

<!-- START: Page Header Banner -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title">Riwayat & Log Akses Pengguna</h1>
    <p class="page-subtitle mb-0">Catatan autentikasi dan aktivitas setiap pengguna yang mengakses database SIMRS.</p>
  </div>
  <div class="mt-3 mt-md-0">
    <a href="index.php" class="btn-custom btn-custom-light">
      <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
  </div>
</div>
<!-- END: Page Header Banner -->

<!-- START: Stat Cards Row -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: var(--radius-lg); background: #ffffff;">
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(2, 132, 199, 0.1); color: #0284C7;">
          <i class="bi bi-clock-history fs-4"></i>
        </div>
        <div>
          <div class="text-muted small">Total Log Tercatat</div>
          <div class="fs-4 fw-bold text-dark"><?= number_format($statTotalLog) ?> <span class="fs-6 fw-normal text-muted">Aktivitas</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-4">
    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: var(--radius-lg); background: #ffffff;">
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981;">
          <i class="bi bi-calendar-check-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small">Aktivitas Hari Ini</div>
          <div class="fs-4 fw-bold text-dark"><?= number_format($statTodayLog) ?> <span class="fs-6 fw-normal text-muted">Sesi</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-4">
    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: var(--radius-lg); background: #ffffff;">
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
          <i class="bi bi-people-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small">User Unik Terdeteksi</div>
          <div class="fs-4 fw-bold text-dark"><?= number_format($statUniqueUser) ?> <span class="fs-6 fw-normal text-muted">Akun</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END: Stat Cards Row -->

<!-- START: Log Table Card Container -->
<div class="table-card-custom">
  <!-- Header Controls -->
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center gap-2">
    <form action="log_akses.php" method="get" class="table-search-box" style="min-width: 320px;">
      <i class="bi bi-search table-search-icon"></i>
      <input type="text" name="search" class="table-search-input" placeholder="Cari nama, username, aktivitas, atau IP..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <div class="d-flex align-items-center gap-2">
      <?php if (!empty($search)): ?>
        <a href="log_akses.php" class="btn-custom btn-custom-sm btn-custom-light">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
      <?php endif; ?>
      <button onclick="window.location.reload();" class="btn-custom btn-custom-sm btn-custom-primary">
        <i class="bi bi-arrow-clockwise"></i> Refresh Log
      </button>
    </div>
  </div>

  <!-- Responsive Table Wrapper -->
  <div class="table-responsive">
    <table class="table-custom mb-0">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Pengguna (User)</th>
          <th>Aktivitas</th>
          <th>Alamat IP</th>
          <th>Perangkat / Browser</th>
          <th>Waktu Akses</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
          <?php 
          $no = 1;
          while ($row = mysqli_fetch_assoc($result)): 
            $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
            if (stripos($row['aktivitas'], 'Gagal') !== false) {
                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            } elseif (stripos($row['aktivitas'], 'Logout') !== false) {
                $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
            } elseif (stripos($row['aktivitas'], 'Login') !== false) {
                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
            } elseif (stripos($row['aktivitas'], 'Hapus') !== false) {
                $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
            }
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background: #E0F2FE; color: #0284C7; font-weight: 700;">
                    <?= strtoupper(substr($row['username'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                    <small class="text-muted">@<?= htmlspecialchars($row['username']) ?></small>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge <?= $badgeClass ?> px-2 py-1" style="font-weight: 600; font-size: 0.78rem;">
                  <?= htmlspecialchars($row['aktivitas']) ?>
                </span>
              </td>
              <td>
                <code class="text-dark bg-light px-2 py-1 rounded" style="font-size: 0.8rem;"><?= htmlspecialchars($row['ip_address']) ?></code>
              </td>
              <td>
                <small class="text-muted" title="<?= htmlspecialchars($row['user_agent']) ?>">
                  <?= htmlspecialchars(substr($row['user_agent'], 0, 35)) ?><?= strlen($row['user_agent']) > 35 ? '...' : '' ?>
                </small>
              </td>
              <td>
                <span class="text-dark small fw-medium">
                  <?= date('d M Y, H:i:s', strtotime($row['waktu'])) ?>
                </span>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center py-5">
              <div class="text-muted">
                <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                <h6 class="text-dark fw-bold mb-1">Belum Ada Riwayat Log</h6>
                <p class="mb-0 small">Belum ada aktivitas yang terekam atau data tidak cocok dengan kata kunci pencarian.</p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="table-footer-control d-flex justify-content-between align-items-center p-3 border-top">
    <span class="table-pagination-info text-muted small">
      Menampilkan <strong><?= ($result) ? mysqli_num_rows($result) : 0 ?></strong> catatan akses terbaru
    </span>
    <span class="small text-muted">
      Database: <code class="text-dark">simrs.log_akses</code>
    </span>
  </div>
</div>
<!-- END: Log Table Card Container -->

<?php include("footer.php"); ?>
