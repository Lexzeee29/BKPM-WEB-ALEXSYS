<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
  <div>
    <p class="text-primary fw-semibold mb-1">DASHBOARD</p>
    <h1 class="h2 mb-1">Selamat datang, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-secondary mb-0">Kelola data akademik melalui menu yang tersedia.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-primary">Mahasiswa</a>
    <a href="<?= BASE_URL ?>/prodi" class="btn btn-outline-primary">Prodi</a>
    <a href="<?= BASE_URL ?>/matakuliah" class="btn btn-outline-primary">Mata Kuliah</a>
  </div>
</div>

