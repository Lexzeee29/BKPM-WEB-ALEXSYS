<?php $item = $item ?? []; $prodi = $prodi ?? []; ?>
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="mb-4">
      <a href="<?= BASE_URL ?>/mahasiswa" class="text-decoration-none">&larr; Kembali ke daftar</a>
      <h1 class="h2 mt-3 mb-1">Edit Mahasiswa</h1>
    </div>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/mahasiswa/<?= $item['id'] ?>" method="post" class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <div class="mb-3">
          <label for="nim" class="form-label">NIM</label>
          <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($item['nim'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="mb-3">
          <label for="prodi" class="form-label">Program Studi</label>
          <select class="form-select" id="prodi" name="prodi_id" required>
            <?php foreach ($prodi ?? [] as $option): ?>
              <option value="<?= $option['id'] ?>" <?= (int) $option['id'] === (int) $item['prodi_id'] ? 'selected' : '' ?>><?= htmlspecialchars($option['kode'] . ' - ' . $option['nama'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="angkatan" class="form-label">Angkatan</label>
          <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="2100" value="<?= htmlspecialchars($item['angkatan'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="mb-4">
          <label for="status" class="form-label">Status</label>
          <select class="form-select" id="status" name="status">
            <?php foreach (['aktif', 'cuti', 'lulus'] as $status): ?>
              <option value="<?= $status ?>" <?= $item['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
          <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-light border">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>