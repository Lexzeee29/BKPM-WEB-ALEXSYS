<?php include __DIR__ . '/../partials/header.php'; ?>
  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <main class="container py-5">
    <?php require $content ?? ''; ?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>