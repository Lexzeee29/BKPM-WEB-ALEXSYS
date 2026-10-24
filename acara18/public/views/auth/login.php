<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="text-center mb-4">
      <h1 class="h2">Login SI Akademik</h1>
      <p class="text-secondary mb-0">Masuk untuk mengelola data mahasiswa.</p>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/login" method="post" class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required autofocus>
        </div>
        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="small text-secondary mt-3 mb-0">Demo: admin / admin123</p>
      </div>
    </form>
  </div>
</div>