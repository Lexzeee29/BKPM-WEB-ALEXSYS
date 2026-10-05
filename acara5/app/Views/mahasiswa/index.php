    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <p class="text-primary fw-semibold mb-1">DATA AKADEMIK</p>
        <h1 class="h2 mb-1">Daftar Mahasiswa</h1>
        <p class="text-secondary mb-0">Kelola data mahasiswa dalam satu tampilan.</p>
      </div>
      <a href="<?= BASE_URL ?>/mahasiswa/create" class="btn btn-primary">
        + Tambah Mahasiswa
      </a>
    </div>

    <section class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th class="px-4">NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Angkatan</th>
                <th class="text-end px-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mahasiswa ?? [] as $index => $item): ?>
                <tr>
                  <td class="px-4"><?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><span class="badge text-bg-light border"><?= $item->getAngkatan() ?></span></td>
                  <td class="text-end px-4">
                    <a href="<?= BASE_URL ?>/mahasiswa/<?= $index + 1 ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Edit</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" disabled>Hapus</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
