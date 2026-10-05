<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Mahasiswa | SI Akademik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="index.php">SI Akademik</a>
    </div>
  </nav>

  <main class="container py-4 py-md-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <p class="text-primary fw-semibold mb-1">DATA AKADEMIK</p>
        <h1 class="h2 mb-1">Daftar Mahasiswa</h1>
        <p class="text-secondary mb-0">Kelola data mahasiswa dalam satu tampilan.</p>
      </div>
      <div class="d-grid d-md-flex">
        <a href="create.php" class="btn btn-primary">
          + Tambah Mahasiswa
        </a>
      </div>
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
                <th class="text-end px-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="px-4">E41230001</td>
                <td>Andi Setiawan</td>
                <td>Teknik Informatika</td>
                <td class="text-end px-4">
                  <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Edit</button>
                  <button type="button" class="btn btn-sm btn-outline-danger" disabled>Hapus</button>
                </td>
              </tr>
              <tr>
                <td class="px-4">E41230002</td>
                <td>Siti Rahma</td>
                <td>Manajemen Informatika</td>
                <td class="text-end px-4">
                  <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Edit</button>
                  <button type="button" class="btn btn-sm btn-outline-danger" disabled>Hapus</button>
                </td>
              </tr>
              <tr>
                <td colspan="4" class="text-center text-secondary py-4">
                  Data contoh ditampilkan sebelum terhubung ke database.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</body>
</html>