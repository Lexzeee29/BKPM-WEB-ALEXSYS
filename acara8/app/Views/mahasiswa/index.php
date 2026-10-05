    <?php $mahasiswa = $mahasiswa ?? []; $search = $search ?? ''; $total = $total ?? 0; $page = $page ?? 1; $totalPages = $totalPages ?? 1; ?>
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
      <div class="card-body border-bottom">
        <form method="get" action="<?= BASE_URL ?>/mahasiswa" class="row g-2">
          <div class="col-md-9">
            <label for="search" class="visually-hidden">Cari mahasiswa</label>
            <input type="search" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari berdasarkan NIM atau nama">
          </div>
          <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-outline-primary">Cari</button>
          </div>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th class="px-4">NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Angkatan</th>
                <th>Status</th>
                <th class="text-end px-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mahasiswa ?? [] as $item): ?>
                <tr>
                  <td class="px-4"><?= htmlspecialchars($item['nim'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($item['prodi_nama'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><span class="badge text-bg-light border"><?= htmlspecialchars($item['angkatan'], ENT_QUOTES, 'UTF-8') ?></span></td>
                  <td><span class="badge text-bg-success"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                  <td class="text-end px-4">
                    <a href="<?= BASE_URL ?>/mahasiswa/<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                    <a href="<?= BASE_URL ?>/mahasiswa/<?= $item['id'] ?>/edit" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="<?= BASE_URL ?>/mahasiswa/<?= $item['id'] ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Hapus data mahasiswa ini?')">
                      <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
      </div>
      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-secondary">Total: <?= $total ?? 0 ?> data</small>
        <?php if (($totalPages ?? 1) > 1): ?>
          <nav aria-label="Pagination mahasiswa">
            <ul class="pagination pagination-sm mb-0">
              <?php for ($number = 1; $number <= $totalPages; $number++): ?>
                <li class="page-item <?= $number === ($page ?? 1) ? 'active' : '' ?>">
                  <a class="page-link" href="<?= BASE_URL ?>/mahasiswa?search=<?= urlencode($search ?? '') ?>&page=<?= $number ?>"><?= $number ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </section>
