<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/dashboard">SI Akademik</a>
    <div class="d-flex align-items-center gap-3">
      <?php if (!empty($_SESSION['logged_in'])): ?>
        <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/dashboard" class="text-white text-decoration-none">Mahasiswa</a>
      <?php endif; ?>
      <span class="navbar-text text-white-50">Praktikum PHP Native</span>
      <?php if (!empty($_SESSION['logged_in'])): ?>
        <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/logout" class="btn btn-sm btn-outline-light">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>