    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="mb-4">
          <a href="<?= BASE_URL ?>/mahasiswa" class="text-decoration-none">&larr; Kembali ke daftar</a>
          <h1 class="h2 mt-3 mb-1">Tambah Mahasiswa</h1>
          <p class="text-secondary mb-0">Lengkapi formulir berikut untuk mencatat mahasiswa baru.</p>
        </div>

        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/mahasiswa" method="post" class="card border-0 shadow-sm">
          <div class="card-body p-4 p-md-5">
            <div class="mb-3">
              <label for="nim" class="form-label">NIM</label>
              <input type="text" class="form-control" id="nim" name="nim" placeholder="Contoh: 2401001" inputmode="numeric" pattern="[0-9]+" value="<?= htmlspecialchars($formData['nim'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
              <label for="nama" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($formData['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" value="<?= htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
              <label for="prodi" class="form-label">Program Studi</label>
              <select class="form-select" id="prodi" name="prodi_id" required>
                <option value="" selected disabled>Pilih program studi</option>
                <?php foreach ($prodi ?? [] as $item): ?>
                  <option value="<?= $item['id'] ?>" <?= isset($formData['prodi_id']) && (int) $formData['prodi_id'] === (int) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars($item['kode'] . ' - ' . $item['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-4">
              <label for="angkatan" class="form-label">Angkatan</label>
              <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="2100" value="<?= htmlspecialchars($formData['angkatan'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
              <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-light border">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
            </div>
          </div>
        </form>
      </div>
    </div>
