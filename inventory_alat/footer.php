    </div><!-- END: p-4 container -->

    <!-- START: Footer Component -->
    <footer class="footer-custom">
      <div class="footer-left">
        <span class="footer-logo">
          <i class="bi bi-hospital"></i> SIMRS Inventory Alat
        </span>
        <span class="footer-separator">|</span>
        <span class="footer-copy">&copy; <?= date('Y') ?> Sistem Informasi Manajemen Rumah Sakit • UI by Spark Admin</span>
      </div>
      <div class="footer-right">
        <ul class="footer-links">
          <li><a href="index.php" class="footer-link">Daftar Alat</a></li>
          <li><a href="add.php" class="footer-link">Tambah Alat</a></li>
          <li><a href="log_akses.php" class="footer-link">Log Akses</a></li>
          <li><a href="logout.php" class="footer-link text-danger" onclick="return confirm('Apakah Anda yakin ingin logout?');">Logout</a></li>
        </ul>
      </div>
    </footer>
    <!-- END: Footer Component -->

  </div>
  <!-- END: Main Content Area -->

  <!-- Script dependencies -->
  <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
  <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="assets/js/dashboard.js"></script>
</body>

</html>
