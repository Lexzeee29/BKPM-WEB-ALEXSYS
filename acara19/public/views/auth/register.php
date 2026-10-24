<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="text-center mb-4">
      <h1 class="h2">Daftar Akun</h1>
      <p class="text-secondary mb-0">Buat akun baru untuk masuk ke SI Akademik.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/register" method="post" class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required autofocus>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Daftar</button>
        <div class="mt-3 text-center">
          <small class="text-secondary">Sudah punya akun? </small>
          <a href="<?= BASE_URL ?>/login" class="small">Login</a>
        </div>
      </div>
    </form>
  </div>
</div>
