<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'SI Akademik', ENT_QUOTES, 'UTF-8') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <main class="container py-5">
    <?php
      $flash = $_SESSION['flash'] ?? null;
      unset($_SESSION['flash']);
      $flashMessage = is_array($flash) ? ($flash['message'] ?? '') : $flash;
      $flashType = is_array($flash) ? ($flash['type'] ?? 'success') : 'success';
    ?>
    <?php if ($flashMessage !== null && $flashMessage !== ''): ?>
      <div class="alert alert-<?= htmlspecialchars($flashType, ENT_QUOTES, 'UTF-8') ?>" role="alert"><?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php require $content ?? ''; ?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>