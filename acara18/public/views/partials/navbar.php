<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= BASE_URL ?>/dashboard">SI Akademik</a>
    <div class="d-flex align-items-center gap-3">
      <?php if (!empty($_SESSION['logged_in'])): ?>
        <a href="<?= BASE_URL ?>/mahasiswa" class="text-white text-decoration-none">Mahasiswa</a>
        <a href="<?= BASE_URL ?>/prodi" class="text-white text-decoration-none">Prodi</a>
        <a href="<?= BASE_URL ?>/matakuliah" class="text-white text-decoration-none">Mata Kuliah</a>
      <?php endif; ?>
      <span class="navbar-text text-white-50">Praktikum PHP Native</span>
      <?php if (!empty($_SESSION['logged_in'])): ?>
        <a href="<?= BASE_URL ?>/logout" class="btn btn-sm btn-outline-light">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>