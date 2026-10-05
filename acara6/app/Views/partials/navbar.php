<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= BASE_URL ?>/mahasiswa">SI Akademik</a>
    <div class="d-flex align-items-center gap-3">
      <span class="navbar-text text-white-50">Praktikum PHP Native</span>
      <?php if (!empty($_SESSION['logged_in'])): ?>
        <a href="<?= BASE_URL ?>/logout" class="btn btn-sm btn-outline-light">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>