<?php $item = $item ?? []; ?>
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="mb-4">
      <a href="<?= BASE_URL ?>/mahasiswa" class="text-decoration-none">&larr; Kembali ke daftar</a>
      <h1 class="h2 mt-3 mb-1">Detail Mahasiswa</h1>
      <p class="text-secondary mb-0">Informasi mahasiswa berdasarkan parameter URL.</p>
    </div>

    <section class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <dl class="row mb-0">
          <dt class="col-sm-4">ID</dt>
          <dd class="col-sm-8"><?= htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">NIM</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item['nim'], ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Nama</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Program Studi</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item['prodi_nama'], ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Angkatan</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item['angkatan'], ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Email</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Status</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?></dd>
        </dl>
      </div>
    </section>
  </div>
</div>