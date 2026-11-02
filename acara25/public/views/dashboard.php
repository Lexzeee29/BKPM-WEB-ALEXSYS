<section class="student-dashboard" id="student-dashboard" data-api-url="<?= BASE_URL ?>/api/mahasiswa" 
data-user-role="<?= ($_SESSION['user_role'] ?? 'user')
 === 'admin' ? 'admin' : 'user' ?>" data-api-token="<?= htmlspecialchars($apiToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <header class="dashboard-heading">
    <div>
      <p class="dashboard-eyebrow">SMART VILLAGE / AKADEMIK</p>
      <h1>Data Mahasiswa</h1>
      <p class="dashboard-subtitle">
        Selamat datang, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8') ?>.
        Pantau data pendidikan dalam satu tempat.
      </p>
    </div>
    <div class="dashboard-actions">
      <a class="btn btn-outline-secondary" id="logout-button" href="<?= BASE_URL ?>/logout">Logout</a>
      <button class="btn btn-dark refresh-button" id="refresh-students" type="button">
        Muat ulang
      </button>
    </div>
  </header>

  <div class="dashboard-stats" aria-label="Ringkasan mahasiswa">
    <article class="stat-panel">
      <span class="stat-label">Total mahasiswa</span>
      <strong id="student-total">--</strong>
      <span class="stat-note">Data dari layanan API</span>
    </article>
    <article class="stat-panel stat-panel-accent">
      <span class="stat-label">Program studi</span>
      <strong id="program-total">--</strong>
      <span class="stat-note">Program yang terwakili</span>
    </article>
    <article class="stat-panel">
      <span class="stat-label">Mahasiswa aktif</span>
      <strong id="active-total">--</strong>
      <span class="stat-note">Status aktif saat ini</span>
    </article>
  </div>

  <section class="student-table-section" aria-labelledby="student-list-title">
    <div class="table-toolbar">
      <div>
        <h2 id="student-list-title">Daftar mahasiswa</h2>
        <p id="student-message" class="table-caption" role="status" aria-live="polite">Memuat data...</p>
      </div>
        <div class="table-toolbar-actions">
          <button class="btn btn-primary" id="add-student" type="button" data-admin-only>Tambah mahasiswa</button>
          <label class="search-field">
            <span class="visually-hidden">Cari mahasiswa</span>
            <input id="student-search" type="search" class="form-control" placeholder="Cari nama atau NIM" autocomplete="off">
          </label>
        </div>
    </div>

    <div class="table-responsive">
      <table class="table student-table align-middle mb-0" id="tabel-mahasiswa">
        <thead>
          <tr>
            <th scope="col">NIM</th>
            <th scope="col">Nama</th>
            <th scope="col">Program studi</th>
            <th scope="col">Angkatan</th>
            <th scope="col">Status</th>
              <th scope="col" data-admin-only>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr><td class="table-placeholder" colspan="5">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </section>

    <dialog class="student-dialog" id="student-dialog" data-admin-only aria-labelledby="student-form-title">
      <form id="student-form">
        <h2 id="student-form-title">Tambah mahasiswa</h2>
        <p class="form-message" id="student-form-message" role="status" aria-live="polite"></p>
        <div class="student-form-grid">
          <label>
            <span>NIM</span>
            <input class="form-control" name="nim" type="text" inputmode="numeric" pattern="[0-9]{9}" maxlength="9" required>
          </label>
          <label>
            <span>Nama</span>
            <input class="form-control" name="nama" type="text" maxlength="100" required>
          </label>
          <label>
            <span>Email</span>
            <input class="form-control" name="email" type="email" maxlength="100" required>
          </label>
          <label>
            <span>Program studi</span>
            <select class="form-select" name="prodi_id" required>
              <option value="1">Teknik Informatika</option>
              <option value="2">Sistem Informasi</option>
              <option value="3">Teknik Komputer</option>
            </select>
          </label>
          <label>
            <span>Angkatan</span>
            <input class="form-control" name="angkatan" type="number" min="2000" max="2155" required>
          </label>
        </div>
        <div class="student-dialog-actions">
          <button class="btn btn-outline-secondary" id="cancel-student" type="button">Batal</button>
          <button class="btn btn-primary" id="save-student" type="submit">Simpan</button>
        </div>
      </form>
    </dialog>
</div>
<script src="<?= BASE_URL ?>/views/app.js" defer></script>

