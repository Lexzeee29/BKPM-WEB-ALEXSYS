<?php
/** @var int $id */
/** @var \App\Models\Mahasiswa $item */
?>
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
          <dd class="col-sm-8"><?= $id ?></dd>

          <dt class="col-sm-4">NIM</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Nama</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Program Studi</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8') ?></dd>

          <dt class="col-sm-4">Angkatan</dt>
          <dd class="col-sm-8"><?= $item->getAngkatan() ?></dd>
        </dl>
      </div>
    </section>
  </div>
</div>