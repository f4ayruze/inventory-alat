<?php
// Pastikan sesi aktif dan terproteksi login
require_once("auth_check.php");

if (!isset($pageTitle)) $pageTitle = 'Inventory Alat Medis - SIMRS';
if (!isset($activeMenu)) $activeMenu = 'index';

$current_nama = $_SESSION['nama_lengkap'] ?? 'Pengguna';
$current_user = $_SESSION['username'] ?? 'user';
$current_role = $_SESSION['role'] ?? 'Staf';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">

  <!-- Local Third-Party Libraries -->
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/libs/apexcharts/apexcharts.css">
  <link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">

  <!-- Main Design System & Sky Blue Theme Stylesheet -->
  <link rel="stylesheet" href="assets/css/main.css">
  <style>
    /* Palette Biru Langit & Putih Lembut */
    body {
      background-color: #F0F4F9 !important;
      color: #0F172A;
    }
    .main-wrapper {
      background-color: #F0F4F9 !important;
    }
    .sidebar-wrapper {
      background-color: #0C1B2A !important; /* Deep Sky Marine / Dark Slate */
      border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .sidebar-brand i {
      color: #38BDF8 !important; /* Biru langit cerah */
    }
    .sidebar-menu-link:hover {
      background-color: rgba(56, 189, 248, 0.08) !important;
      color: #FFFFFF !important;
    }
    .sidebar-menu-link.active {
      background-color: rgba(56, 189, 248, 0.12) !important;
      color: #FFFFFF !important;
    }
    .sidebar-menu-link.active i {
      color: #38BDF8 !important;
    }
    .sidebar-menu-link.active::before {
      background-color: #38BDF8 !important;
    }

    /* Kartu & Tabel Putih Lembut */
    .card, .table-card-custom {
      background-color: #FFFFFF !important;
      border: 1px solid #E2E8F0 !important;
      box-shadow: 0 4px 15px rgba(12, 27, 42, 0.04) !important;
      border-radius: 16px !important;
    }
    .table-custom thead tr {
      background-color: #F8FAFC !important;
      border-bottom: 2px solid #E2E8F0 !important;
    }
    .table-custom tbody tr:hover {
      background-color: #F0F7FF !important; /* Sorot baris dengan biru langit sangat lembut */
    }

    /* Banner Biru Langit */
    .alert-green-card {
      background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%) !important;
      color: #FFFFFF !important;
      border-radius: 16px !important;
      box-shadow: 0 8px 24px rgba(2, 132, 199, 0.2) !important;
    }
    .alert-green-badge {
      background: rgba(255, 255, 255, 0.2) !important;
      color: #FFFFFF !important;
    }
    .alert-green-badge::before {
      background-color: #38BDF8 !important;
    }
    .alert-green-link {
      color: #E0F2FE !important;
      font-weight: 700 !important;
    }
    .alert-green-link:hover {
      color: #FFFFFF !important;
    }

    /* Tombol Biru Langit */
    .btn-custom-primary {
      background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%) !important;
      border: none !important;
      color: #FFFFFF !important;
      box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
    }
    .btn-custom-primary:hover {
      background: linear-gradient(135deg, #0369A1 0%, #0284C7 100%) !important;
      color: #FFFFFF !important;
      box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35) !important;
    }
    .btn-custom-secondary {
      background-color: #E0F2FE !important;
      color: #0369A1 !important;
      border: 1px solid #BAE6FD !important;
    }
    .btn-custom-secondary:hover {
      background-color: #BAE6FD !important;
      color: #0284C7 !important;
    }
    .table-btn-action:hover {
      background-color: #0284C7 !important;
      border-color: #0284C7 !important;
      color: #FFFFFF !important;
    }

    /* Badge & Indikator */
    .badge-lokasi {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.35rem 0.65rem;
      border-radius: 6px;
      display: inline-block;
      background-color: #E0F2FE !important;
      color: #0284C7 !important;
      border: 1px solid #BAE6FD !important;
    }
    .badge-tahun {
      font-size: 0.8rem;
      font-weight: 600;
      color: #475569;
    }
    .table-order-id {
      font-weight: 700;
      color: #0284C7 !important;
    }
    .status-pill-active {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.75rem;
      padding: 4px 10px;
      border-radius: 20px;
      background: #E0F2FE;
      color: #0369A1;
      font-weight: 600;
    }
    .status-dot-green {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background-color: #0284C7;
    }
  </style>
</head>

<body>

  <!-- START: Sidebar Component -->
  <div class="sidebar-wrapper" id="sidebar">
    <!-- Brand Logo / Identity -->
    <a href="index.php" class="sidebar-brand">
      <i class="bi bi-hospital"></i>
      <span>SIMRS Alat</span>
    </a>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-y-auto">
      <!-- Group: Main Menu -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Menu Utama</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php" class="sidebar-menu-link <?= ($activeMenu == 'index') ? 'active' : '' ?>" title="Daftar Alat">
              <i class="bi bi-grid-fill"></i>
              <span>Daftar Alat</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="add.php" class="sidebar-menu-link <?= ($activeMenu == 'add') ? 'active' : '' ?>" title="Tambah Alat">
              <i class="bi bi-plus-circle-fill"></i>
              <span>Tambah Alat</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="log_akses.php" class="sidebar-menu-link <?= ($activeMenu == 'log_akses') ? 'active' : '' ?>" title="Riwayat & Log Akses">
              <i class="bi bi-shield-check"></i>
              <span>Log Akses User</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Group: Database Shortcut -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Database & Pengaturan</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=simrs" target="_blank" class="sidebar-menu-link" title="Buka Database simrs di phpMyAdmin">
              <i class="bi bi-database"></i>
              <span>MySQL simrs</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="logout.php" class="sidebar-menu-link text-danger" title="Keluar dari Sistem" onclick="return confirm('Apakah Anda yakin ingin logout?');">
              <i class="bi bi-box-arrow-right"></i>
              <span>Logout</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Sidebar Profile Card -->
    <div class="sidebar-profile">
      <div class="d-flex align-items-center justify-content-center rounded-circle me-2" style="width: 40px; height: 40px; background: #0284C7; color: #FFFFFF; font-weight: 700;">
        <?= strtoupper(substr($current_user, 0, 1)) ?>
      </div>
      <div class="sidebar-profile-info">
        <div class="sidebar-profile-name"><?= htmlspecialchars($current_nama) ?></div>
        <div class="sidebar-profile-email"><?= htmlspecialchars($current_role) ?></div>
      </div>
    </div>
  </div>
  <!-- END: Sidebar Component -->

  <!-- START: Main Content Area -->
  <div class="main-wrapper">

    <!-- START: Top Navbar Component -->
    <header class="navbar-custom">
      <div class="navbar-left">
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
          id="desktop-sidebar-toggle" aria-label="Minimize Sidebar">
          <i class="bi bi-chevron-bar-left"></i>
        </button>
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Toggle Navigation">
          <i class="bi bi-list"></i>
        </button>

        <a href="add.php" class="btn-custom btn-custom-sm btn-custom-primary ms-2 d-none d-sm-inline-flex">
          <i class="bi bi-plus-lg"></i>
          <span>Tambah Alat</span>
        </a>
        <a href="log_akses.php" class="btn-custom btn-custom-sm btn-custom-secondary ms-2 d-none d-md-inline-flex">
          <i class="bi bi-clock-history"></i>
          <span>Log Akses</span>
        </a>
      </div>

      <!-- Mid navbar: search pill -->
      <form action="index.php" method="get" class="navbar-search-wrapper">
        <input type="text" name="search" class="navbar-search-input" placeholder="Cari nama alat, merek, atau lokasi..." 
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit" class="navbar-search-btn" aria-label="Search">
          <i class="bi bi-search"></i>
        </button>
      </form>

      <!-- Right actions -->
      <div class="navbar-actions">
        <button class="navbar-action-btn me-1" aria-label="Toggle Fullscreen" id="btn-fullscreen">
          <i class="bi bi-arrows-fullscreen"></i>
        </button>

        <!-- Profile Dropdown -->
        <div class="dropdown ms-2">
          <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false" id="profile-dropdown">
            <div class="d-flex align-items-center justify-content-center rounded-circle me-1" style="width: 32px; height: 32px; background: #0284C7; color: #FFFFFF; font-weight: 700; font-size: 0.85rem;">
              <?= strtoupper(substr($current_user, 0, 1)) ?>
            </div>
            <span class="navbar-profile-name d-none d-md-inline"><?= htmlspecialchars($current_nama) ?></span>
            <i class="bi bi-chevron-down navbar-profile-caret"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
            <li class="dropdown-header">
              <strong><?= htmlspecialchars($current_nama) ?></strong><br>
              <small class="text-muted">@<?= htmlspecialchars($current_user) ?> (<?= htmlspecialchars($current_role) ?>)</small>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="index.php"><i class="bi bi-grid me-2 text-primary"></i> Daftar Alat</a></li>
            <li><a class="dropdown-item" href="add.php"><i class="bi bi-plus-circle me-2 text-success"></i> Tambah Alat</a></li>
            <li><a class="dropdown-item" href="log_akses.php"><i class="bi bi-clock-history me-2 text-info"></i> Log Akses Pengguna</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </header>
    <!-- END: Top Navbar Component -->

    <div class="p-4">
      <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] == 'added'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <strong>Berhasil!</strong> Data alat medis baru berhasil disimpan ke database.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php elseif ($_GET['msg'] == 'updated'): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <strong>Berhasil!</strong> Perubahan data alat medis berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php elseif ($_GET['msg'] == 'deleted'): ?>
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-trash-fill me-2"></i> <strong>Berhasil!</strong> Data alat medis telah dihapus dari tabel database.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
      <?php endif; ?>
