<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
  <div>
    <p class="text-primary fw-semibold mb-1">DASHBOARD</p>
    <h1 class="h2 mb-1">Selamat datang, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-secondary mb-0">Kelola data akademik melalui menu yang tersedia.</p>
  </div>
  <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-primary">Kelola Mahasiswa</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-4">
    <?php if (!empty($flash = $_SESSION['flash'] ?? null)): unset($_SESSION['flash']); ?>
      <div class="alert alert-success mb-0"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
  </div>
</div>