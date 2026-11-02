<section class="student-dashboard" id="student-dashboard" data-api-url="<?= BASE_URL ?>/api/mahasiswa">
  <header class="dashboard-heading">
    <div>
      <p class="dashboard-eyebrow">SMART VILLAGE / AKADEMIK</p>
      <h1>Data Mahasiswa</h1>
      <p class="dashboard-subtitle">
        Selamat datang, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8') ?>.
        Pantau data pendidikan dalam satu tempat.
      </p>
    </div>
    <button class="btn btn-dark refresh-button" id="refresh-students" type="button">
      Muat ulang
    </button>
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
      <label class="search-field">
        <span class="visually-hidden">Cari mahasiswa</span>
        <input id="student-search" type="search" class="form-control" placeholder="Cari nama atau NIM" autocomplete="off">
      </label>
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
          </tr>
        </thead>
        <tbody>
          <tr><td class="table-placeholder" colspan="5">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </section>
</div>
<script src="<?= BASE_URL ?>/views/app.js" defer></script>

