(() => {
  const dashboard = document.querySelector('#student-dashboard');

  if (!dashboard) {
    return;
  }

  const apiUrl = dashboard.dataset.apiUrl;
  const tableBody = dashboard.querySelector('#tabel-mahasiswa tbody');
  const message = dashboard.querySelector('#student-message');
  const search = dashboard.querySelector('#student-search');
  const refreshButton = dashboard.querySelector('#refresh-students');
  let students = [];

  const showRows = () => {
    const query = search.value.trim().toLocaleLowerCase('id');
    const visibleStudents = students.filter((student) =>
      [student.nim, student.nama, student.prodi]
        .some((value) => String(value ?? '').toLocaleLowerCase('id').includes(query))
    );

    tableBody.replaceChildren();

    if (visibleStudents.length === 0) {
      const row = document.createElement('tr');
      const cell = document.createElement('td');
      cell.colSpan = 5;
      cell.className = 'table-placeholder';
      cell.textContent = students.length === 0
        ? 'Belum ada data'
        : 'Tidak ada mahasiswa yang cocok dengan pencarian.';
      row.append(cell);
      tableBody.append(row);
      return;
    }

    visibleStudents.forEach((student) => {
      const row = document.createElement('tr');
      const values = [student.nim, student.nama, student.prodi, student.angkatan];

      values.forEach((value) => {
        const cell = document.createElement('td');
        cell.textContent = String(value ?? '-');
        row.append(cell);
      });

      const statusCell = document.createElement('td');
      const status = document.createElement('span');
      status.className = `status-label status-${String(student.status ?? 'lainnya').toLowerCase()}`;
      status.textContent = String(student.status ?? 'Tidak diketahui');
      statusCell.append(status);
      row.append(statusCell);
      tableBody.append(row);
    });

    message.textContent = `Menampilkan ${visibleStudents.length} dari ${students.length} mahasiswa.`;
  };

  const loadStudents = async () => {
    refreshButton.disabled = true;
    message.textContent = 'Memuat data...';

    try {
      const response = await fetch(apiUrl, {
        headers: { Accept: 'application/json' },
      });
      const result = await response.json();

      if (!response.ok || result.success !== true || !Array.isArray(result.data)) {
        throw new Error(result.message || 'Respons API tidak sesuai.');
      }

      students = result.data;
      dashboard.querySelector('#student-total').textContent = String(students.length);
      dashboard.querySelector('#program-total').textContent = String(
        new Set(students.map((student) => student.prodi).filter(Boolean)).size
      );
      dashboard.querySelector('#active-total').textContent = String(
        students.filter((student) => student.status === 'aktif').length
      );
      showRows();
    } catch (error) {
      students = [];
      dashboard.querySelector('#student-total').textContent = '0';
      dashboard.querySelector('#program-total').textContent = '0';
      dashboard.querySelector('#active-total').textContent = '0';
      showRows();
      message.textContent = `Gagal memuat data: ${error.message}`;
    } finally {
      refreshButton.disabled = false;
    }
  };

  search.addEventListener('input', showRows);
  refreshButton.addEventListener('click', loadStudents);
  loadStudents();
})();