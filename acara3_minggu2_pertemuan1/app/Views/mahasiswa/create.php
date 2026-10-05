<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Mahasiswa | SI Akademik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="index.php">SI Akademik</a>
    </div>
  </nav>

  <main class="container py-4 py-md-5">
    <div class="row justify-content-center">
      <div class="col-lg-7 col-xl-6">
        <div class="mb-4">
          <a href="index.php" class="text-decoration-none">&larr; Kembali ke daftar</a>
          <h1 class="h2 mt-3 mb-1">Tambah Mahasiswa</h1>
          <p class="text-secondary mb-0">Lengkapi formulir berikut untuk mencatat mahasiswa baru.</p>
        </div>

        <form action="/mahasiswa/store" method="post" class="card border-0 shadow-sm">
          <div class="card-body p-3 p-sm-4 p-md-5">
            <div class="mb-3">
              <label for="nim" class="form-label">NIM</label>
              <input type="text" class="form-control" id="nim" name="nim" placeholder="Contoh: E41230001" required>
            </div>
            <div class="mb-3">
              <label for="nama" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-4">
              <label for="prodi" class="form-label">Program Studi</label>
              <select class="form-select" id="prodi" name="prodi" required>
                <option value="" selected disabled>Pilih program studi</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Manajemen Informatika">Manajemen Informatika</option>
                <option value="Teknik Komputer">Teknik Komputer</option>
              </select>
            </div>
            <div class="d-grid d-sm-flex justify-content-sm-end gap-2">
              <a href="index.php" class="btn btn-light border">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>
</html>